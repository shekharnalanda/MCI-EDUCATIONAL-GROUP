<?php

namespace App\Services;

use App\Models\AutoReplyRule;
use App\Models\CommunicationLog;
use App\Models\Enquiry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AutoReplyEngine
{
    public function process(Enquiry $enquiry): array
    {
        $enquiry->loadMissing(['institution','customer']);
        $institution = $enquiry->institution;

        if (!$institution || !$institution->auto_reply_enabled) {
            $enquiry->update(['auto_reply_status' => 'disabled']);
            return ['status' => 'disabled'];
        }

        $text = $this->normalize(implode(' ', array_filter([
            $enquiry->subject,
            $enquiry->message,
            $enquiry->category,
            $enquiry->course_service,
        ])));

        $rules = AutoReplyRule::with('template')
            ->where(function ($query) use ($institution) {
                $query->where('institution_id', $institution->id)
                    ->orWhereNull('institution_id');
            })
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        $matches = $rules->filter(function (AutoReplyRule $rule) use ($text) {
            $keywords = collect($rule->keywords ?? [])->map(fn ($keyword) => $this->normalize((string)$keyword))->filter();
            if ($keywords->isEmpty()) {
                return false;
            }
            return $keywords->contains(fn ($keyword) => Str::contains($text, $keyword));
        });

        $rule = $matches->first(fn (AutoReplyRule $item) => $item->auto_send && $item->template && in_array($item->template->status, ['approved','live'], true));

        if (!$rule) {
            $enquiry->update(['auto_reply_status' => 'manual_review']);
            return ['status' => 'manual_review'];
        }

        if (!$enquiry->email) {
            $enquiry->update(['auto_reply_status' => 'no_email']);
            return ['status' => 'no_email', 'rule_id' => $rule->id];
        }

        $template = $rule->template;
        $subject = $this->render($template->subject ?: 'Regarding your enquiry', $enquiry);
        $body = $this->render($template->body, $enquiry);
        $senderEmail = $institution->sender_email ?: config('mail.from.address');
        $senderName = $institution->sender_name ?: $institution->name;

        $log = CommunicationLog::create([
            'enquiry_id' => $enquiry->id,
            'customer_id' => $enquiry->customer_id,
            'institution_id' => $institution->id,
            'auto_reply_rule_id' => $rule->id,
            'channel' => 'email',
            'direction' => 'outgoing',
            'reply_type' => 'auto',
            'subject' => $subject,
            'message_body' => $body,
            'sender' => $senderEmail,
            'recipient' => $enquiry->email,
            'delivery_status' => 'pending',
        ]);

        try {
            Mail::raw($body, function ($message) use ($enquiry, $institution, $subject, $senderEmail, $senderName) {
                $message->to($enquiry->email, $enquiry->name)->subject($subject);
                if ($senderEmail) {
                    $message->from($senderEmail, $senderName);
                }
                if ($institution->reply_to_email) {
                    $message->replyTo($institution->reply_to_email, $senderName);
                }
            });

            $log->update(['delivery_status' => 'sent', 'sent_at' => now()]);
            $enquiry->update(['auto_reply_status' => 'sent', 'last_replied_at' => now(), 'status' => $enquiry->status === 'new' ? 'replied' : $enquiry->status]);

            return ['status' => 'sent', 'rule_id' => $rule->id, 'communication_id' => $log->id];
        } catch (\Throwable $e) {
            Log::warning('Central auto reply failed', ['enquiry_id' => $enquiry->id, 'error' => $e->getMessage()]);
            $log->update(['delivery_status' => 'failed', 'failed_reason' => $e->getMessage()]);
            $enquiry->update(['auto_reply_status' => 'failed']);
            return ['status' => 'failed', 'rule_id' => $rule->id];
        }
    }

    private function render(string $text, Enquiry $enquiry): string
    {
        $institution = $enquiry->institution;
        $replacements = [
            '{customer_name}' => $enquiry->name ?: 'Customer',
            '{business_name}' => $institution?->name ?? 'MCI Educational Group',
            '{course_name}' => $enquiry->course_service ?: ($enquiry->subject ?: 'requested service'),
            '{website_url}' => $institution?->website_url ?? '',
            '{contact_number}' => $institution?->phone ?? '',
            '{customer_phone}' => $enquiry->phone ?? '',
            '{customer_email}' => $enquiry->email ?? '',
        ];
        return strtr($text, $replacements);
    }

    private function normalize(string $text): string
    {
        $text = Str::lower($text);
        $text = preg_replace('/[^\pL\pN\s]+/u', ' ', $text) ?? $text;
        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }
}
