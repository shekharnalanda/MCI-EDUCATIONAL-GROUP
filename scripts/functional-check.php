<?php

declare(strict_types=1);

use App\Models\AutoReplyRule;
use App\Models\CentralAdmission;
use App\Models\CommunicationLog;
use App\Models\Customer;
use App\Models\Download;
use App\Models\Enquiry;
use App\Models\FollowUp;
use App\Models\GalleryItem;
use App\Models\Institution;
use App\Models\NewsPost;
use App\Models\ReplyTemplate;
use App\Models\Setting;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$models = [
    'Institution' => Institution::class,
    'Customer' => Customer::class,
    'Enquiry' => Enquiry::class,
    'CentralAdmission' => CentralAdmission::class,
    'ReplyTemplate' => ReplyTemplate::class,
    'AutoReplyRule' => AutoReplyRule::class,
    'CommunicationLog' => CommunicationLog::class,
    'FollowUp' => FollowUp::class,
    'NewsPost' => NewsPost::class,
    'GalleryItem' => GalleryItem::class,
    'Download' => Download::class,
    'Setting' => Setting::class,
];

$before = [];
foreach ($models as $name => $class) {
    $before[$name] = $class::count();
}

$pass = 0;
$fail = 0;

function check(bool $condition, string $label): void
{
    global $pass, $fail;
    if ($condition) {
        $pass++;
        echo "PASS  {$label}\n";
        return;
    }
    $fail++;
    echo "FAIL  {$label}\n";
}

$suffix = date('YmdHis').'-'.bin2hex(random_bytes(3));

DB::beginTransaction();

try {
    $institution = Institution::create([
        'name' => 'Functional Test Institution',
        'slug' => 'functional-test-'.$suffix,
        'website_url' => 'https://example.com',
        'sender_name' => 'Functional Test',
        'sender_email' => 'functional-sender@example.com',
        'reply_to_email' => 'functional-reply@example.com',
        'phone' => '9999999999',
        'short_description' => 'Temporary rollback-safe test',
        'display_order' => 9999,
        'is_active' => false,
        'auto_reply_enabled' => false,
        'sync_enabled' => false,
    ]);
    check($institution->exists, 'Institution create');

    $customer = Customer::create([
        'name' => 'Functional Customer',
        'mobile' => '9999999998',
        'email' => 'functional-customer@example.com',
        'first_institution_id' => $institution->id,
        'last_activity_at' => now(),
    ]);
    check($customer->exists, 'Customer create');

    $enquiry = Enquiry::create([
        'institution_id' => $institution->id,
        'customer_id' => $customer->id,
        'name' => $customer->name,
        'phone' => $customer->mobile,
        'email' => $customer->email,
        'subject' => 'Functional central enquiry',
        'message' => 'Please share course fee and duration.',
        'status' => 'new',
        'source_site' => 'functional.example.com',
        'source_reference_id' => 'functional-enquiry-'.$suffix,
        'category' => 'course_fee',
        'priority' => 'normal',
        'auto_reply_status' => 'pending',
        'received_at' => now(),
        'sync_status' => 'synced',
    ]);
    check($enquiry->exists, 'Central enquiry create');
    $enquiry->update(['status' => 'in_progress']);
    check($enquiry->fresh()?->status === 'in_progress', 'Central enquiry update');

    $admission = CentralAdmission::create([
        'institution_id' => $institution->id,
        'customer_id' => $customer->id,
        'enquiry_id' => $enquiry->id,
        'source_site' => 'functional.example.com',
        'source_reference_id' => 'functional-admission-'.$suffix,
        'application_reference' => 'FT-'.$suffix,
        'applicant_name' => $customer->name,
        'phone' => $customer->mobile,
        'email' => $customer->email,
        'course_program' => 'DCA',
        'status' => 'new',
        'payment_status' => 'pending',
        'submitted_at' => now(),
    ]);
    check($admission->exists, 'Central admission create');

    $template = ReplyTemplate::create([
        'institution_id' => $institution->id,
        'name' => 'Functional Fee Template',
        'category' => 'course_fee',
        'language' => 'en',
        'subject' => 'Course fee information',
        'body' => 'Hello {customer_name}, thank you for contacting {business_name}.',
        'status' => 'test',
        'is_active' => true,
    ]);
    check($template->exists, 'Reply template create');

    $rule = AutoReplyRule::create([
        'institution_id' => $institution->id,
        'reply_template_id' => $template->id,
        'name' => 'Functional Fee Rule',
        'category' => 'course_fee',
        'keywords' => ['fee', 'fees', 'फीस'],
        'priority' => 100,
        'auto_send' => false,
        'is_active' => true,
        'fallback_action' => 'manual_review',
    ]);
    check($rule->exists && is_array($rule->keywords), 'Auto reply rule create/cast');

    $communication = CommunicationLog::create([
        'enquiry_id' => $enquiry->id,
        'customer_id' => $customer->id,
        'institution_id' => $institution->id,
        'auto_reply_rule_id' => $rule->id,
        'channel' => 'email',
        'direction' => 'outgoing',
        'reply_type' => 'manual',
        'subject' => 'Functional message',
        'message_body' => 'Rollback-safe functional communication.',
        'sender' => 'functional-sender@example.com',
        'recipient' => $customer->email,
        'delivery_status' => 'pending',
    ]);
    check($communication->exists, 'Communication log create');

    $followUp = FollowUp::create([
        'enquiry_id' => $enquiry->id,
        'scheduled_at' => now()->addDay(),
        'status' => 'pending',
        'note' => 'Rollback-safe functional follow-up.',
    ]);
    check($followUp->exists && $followUp->scheduled_at !== null, 'Follow-up create/cast');

    $enquiry->delete();
    check($enquiry->trashed(), 'Enquiry soft delete');
    $enquiry->restore();
    check(! $enquiry->fresh()->trashed(), 'Enquiry restore');

    $admission->delete();
    check($admission->trashed(), 'Admission soft delete');
    $admission->restore();
    check(! $admission->fresh()->trashed(), 'Admission restore');

    $news = NewsPost::create([
        'title' => 'Functional Test News',
        'slug' => 'functional-test-news-'.$suffix,
        'excerpt' => 'Temporary rollback-safe test',
        'content' => 'Temporary rollback-safe test content',
        'published_at' => now(),
        'is_active' => false,
    ]);
    check($news->exists, 'News create');

    $gallery = GalleryItem::create([
        'title' => 'Functional Test Gallery',
        'image' => '/storage/functional-test.jpg',
        'caption' => 'Temporary rollback-safe test',
        'display_order' => 9999,
        'is_active' => false,
    ]);
    check($gallery->exists, 'Gallery create');

    $download = Download::create([
        'title' => 'Functional Test Download',
        'description' => 'Temporary rollback-safe test',
        'external_url' => 'https://example.com/test-download',
        'display_order' => 9999,
        'is_active' => false,
    ]);
    check($download->exists, 'Download create');

    $setting = Setting::create([
        'key' => 'functional_test_'.$suffix,
        'value' => 'temporary',
    ]);
    check($setting->exists, 'Setting create');

    check($customer->enquiries()->whereKey($enquiry->id)->exists(), 'Customer-enquiry relationship');
    check($customer->admissions()->whereKey($admission->id)->exists(), 'Customer-admission relationship');
    check($enquiry->communicationLogs()->whereKey($communication->id)->exists(), 'Enquiry-communication relationship');
    check($enquiry->followUps()->whereKey($followUp->id)->exists(), 'Enquiry-follow-up relationship');
} catch (Throwable $e) {
    $fail++;
    echo 'FAIL  Exception: '.$e->getMessage()."\n";
} finally {
    DB::rollBack();
}

foreach ($models as $name => $class) {
    check($class::count() === $before[$name], "{$name} rollback preserved live count");
}

echo "===== FUNCTIONAL RESULT =====\n";
echo "PASS={$pass} FAIL={$fail}\n";
echo $fail === 0 ? "MCI_FUNCTIONAL=PASS\n" : "MCI_FUNCTIONAL=FAIL\n";

exit($fail === 0 ? 0 : 1);
