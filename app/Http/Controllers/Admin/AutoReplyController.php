<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoReplyRule;
use App\Models\Institution;
use App\Models\ReplyTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AutoReplyController extends Controller
{
    public function index()
    {
        return view('admin.auto-replies.index', [
            'rules' => AutoReplyRule::with(['institution','template'])->orderBy('priority')->latest()->get(),
            'templates' => ReplyTemplate::with('institution')->latest()->get(),
            'institutions' => Institution::orderBy('display_order')->orderBy('name')->get(),
            'testResult' => session('testResult'),
        ]);
    }

    public function storeTemplate(Request $request)
    {
        $data = $request->validate([
            'institution_id' => 'nullable|exists:institutions,id','name' => 'required|string|max:180','category' => 'required|string|max:80',
            'language' => 'required|in:en,hi,roman_hi,bilingual','subject' => 'nullable|string|max:180','body' => 'required|string|max:10000','status' => 'required|in:draft,test,approved,live',
        ]);
        $data['is_active'] = true;
        ReplyTemplate::create($data);
        return back()->with('success','Reply template created.');
    }

    public function storeRule(Request $request)
    {
        $data = $request->validate([
            'institution_id' => 'nullable|exists:institutions,id','reply_template_id' => 'required|exists:reply_templates,id','name' => 'required|string|max:180',
            'category' => 'required|string|max:80','keywords_text' => 'nullable|string|max:3000','priority' => 'required|integer|min:1|max:10000',
            'fallback_action' => 'required|in:manual_review,acknowledge_only,do_nothing',
        ]);
        $keywords = collect(preg_split('/[,\n]+/', (string)($data['keywords_text'] ?? '')))->map(fn($v) => trim($v))->filter()->values()->all();
        unset($data['keywords_text']);
        $data['keywords'] = $keywords;
        $data['auto_send'] = $request->boolean('auto_send');
        $data['is_active'] = true;
        AutoReplyRule::create($data);
        return back()->with('success','Auto-reply rule created.');
    }

    public function test(Request $request)
    {
        $data = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'message' => 'required|string|max:5000',
            'customer_name' => 'nullable|string|max:120',
            'course_service' => 'nullable|string|max:180',
        ]);
        $institution = Institution::findOrFail($data['institution_id']);
        $text = Str::lower($data['message']);
        $rules = AutoReplyRule::with('template')->where('is_active', true)
            ->where(fn($q) => $q->where('institution_id',$institution->id)->orWhereNull('institution_id'))->orderBy('priority')->get();
        $matched = $rules->first(function ($rule) use ($text) {
            $keywords = collect($rule->keywords ?: [])->map(fn($v) => Str::lower(trim((string)$v)))->filter();
            return $keywords->isNotEmpty() && $keywords->contains(fn($keyword) => Str::contains($text, $keyword));
        });
        if (!$matched || !$matched->template) {
            return back()->with('testResult', ['matched' => false, 'message' => 'No safe rule matched. This enquiry would go to Manual Review.']);
        }
        $body = strtr($matched->template->body, [
            '{customer_name}' => $data['customer_name'] ?: 'Customer',
            '{business_name}' => $institution->name,
            '{course_name}' => $data['course_service'] ?: 'requested course/service',
            '{website_url}' => $institution->website_url ?: '',
            '{contact_number}' => $institution->phone ?: '',
            '{customer_phone}' => '',
            '{customer_email}' => '',
        ]);
        return back()->with('testResult', ['matched' => true,'rule' => $matched->name,'template' => $matched->template->name,'subject' => $matched->template->subject,'body' => $body]);
    }

    public function toggleRule(AutoReplyRule $rule) { $rule->update(['is_active' => !$rule->is_active]); return back()->with('success','Rule status updated.'); }
    public function toggleBusiness(Institution $institution) { $institution->update(['auto_reply_enabled' => !$institution->auto_reply_enabled]); return back()->with('success','Business auto-reply setting updated.'); }
}
