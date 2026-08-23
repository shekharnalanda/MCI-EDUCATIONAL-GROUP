<?php

declare(strict_types=1);

use App\Models\Download;
use App\Models\Enquiry;
use App\Models\GalleryItem;
use App\Models\Institution;
use App\Models\NewsPost;
use App\Models\Setting;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$models = [
    'Institution' => Institution::class,
    'NewsPost' => NewsPost::class,
    'GalleryItem' => GalleryItem::class,
    'Download' => Download::class,
    'Enquiry' => Enquiry::class,
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
        'short_description' => 'Temporary rollback-safe test',
        'display_order' => 9999,
        'is_active' => false,
    ]);
    check($institution->exists, 'Institution create');
    $institution->update(['name' => 'Functional Test Institution Updated']);
    check($institution->fresh()?->name === 'Functional Test Institution Updated', 'Institution update');
    $institutionId = $institution->id;
    $institution->delete();
    check(! Institution::whereKey($institutionId)->exists(), 'Institution delete');

    $news = NewsPost::create([
        'title' => 'Functional Test News',
        'slug' => 'functional-test-news-'.$suffix,
        'excerpt' => 'Temporary rollback-safe test',
        'content' => 'Temporary rollback-safe test content',
        'published_at' => now(),
        'is_active' => false,
    ]);
    check($news->exists, 'News create');
    $news->update(['title' => 'Functional Test News Updated']);
    check($news->fresh()?->title === 'Functional Test News Updated', 'News update');
    $newsId = $news->id;
    $news->delete();
    check(! NewsPost::whereKey($newsId)->exists(), 'News delete');

    $gallery = GalleryItem::create([
        'title' => 'Functional Test Gallery',
        'image' => '/storage/functional-test.jpg',
        'caption' => 'Temporary rollback-safe test',
        'display_order' => 9999,
        'is_active' => false,
    ]);
    check($gallery->exists, 'Gallery create');
    $gallery->update(['caption' => 'Functional Test Gallery Updated']);
    check($gallery->fresh()?->caption === 'Functional Test Gallery Updated', 'Gallery update');
    $galleryId = $gallery->id;
    $gallery->delete();
    check(! GalleryItem::whereKey($galleryId)->exists(), 'Gallery delete');

    $download = Download::create([
        'title' => 'Functional Test Download',
        'description' => 'Temporary rollback-safe test',
        'external_url' => 'https://example.com/test-download',
        'display_order' => 9999,
        'is_active' => false,
    ]);
    check($download->exists, 'Download create');
    $download->update(['description' => 'Functional Test Download Updated']);
    check($download->fresh()?->description === 'Functional Test Download Updated', 'Download update');
    $downloadId = $download->id;
    $download->delete();
    check(! Download::whereKey($downloadId)->exists(), 'Download delete');

    $enquiry = Enquiry::create([
        'name' => 'Functional Test Enquiry',
        'phone' => '9999999999',
        'email' => 'functional-test@example.com',
        'subject' => 'Functional test',
        'message' => 'Temporary rollback-safe test',
        'status' => 'new',
    ]);
    check($enquiry->exists, 'Enquiry create');
    $enquiry->update(['status' => 'resolved']);
    check($enquiry->fresh()?->status === 'resolved', 'Enquiry update');
    $enquiryId = $enquiry->id;
    $enquiry->delete();
    check(! Enquiry::whereKey($enquiryId)->exists(), 'Enquiry delete');

    $setting = Setting::create([
        'key' => 'functional_test_'.$suffix,
        'value' => 'temporary',
    ]);
    check($setting->exists, 'Setting create');
    $setting->update(['value' => 'updated']);
    check($setting->fresh()?->value === 'updated', 'Setting update');
    $settingId = $setting->id;
    $setting->delete();
    check(! Setting::whereKey($settingId)->exists(), 'Setting delete');
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
