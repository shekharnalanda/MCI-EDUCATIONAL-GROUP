<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AutoReplyController;
use App\Http\Controllers\Admin\CentralEnquiryController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::view('/about', 'about')->name('about');
Route::get('/institutions', [PublicSiteController::class, 'institutions'])->name('institutions');
Route::view('/programs', 'programs')->name('programs');
Route::get('/news-events', [PublicSiteController::class, 'news'])->name('news-events');
Route::get('/gallery', [PublicSiteController::class, 'gallery'])->name('gallery');
Route::get('/downloads', [PublicSiteController::class, 'downloads'])->name('downloads');
Route::view('/career', 'career')->name('career');
Route::view('/contact', 'contact')->name('contact');
Route::post('/enquiry', [EnquiryController::class, 'store'])->middleware('throttle:10,1')->name('enquiry.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/institutions', [InstitutionController::class, 'index'])->name('institutions.index');
        Route::post('/institutions', [InstitutionController::class, 'store'])->name('institutions.store');
        Route::put('/institutions/{institution}', [InstitutionController::class, 'update'])->name('institutions.update');
        Route::post('/institutions/{institution}/api-token', [InstitutionController::class, 'generateToken'])->name('institutions.api-token');
        Route::delete('/institutions/{institution}', [InstitutionController::class, 'destroy'])->name('institutions.destroy');

        Route::get('/news', [ContentController::class, 'news'])->name('news.index');
        Route::post('/news', [ContentController::class, 'storeNews'])->name('news.store');
        Route::put('/news/{newsPost}', [ContentController::class, 'updateNews'])->name('news.update');
        Route::delete('/news/{newsPost}', [ContentController::class, 'deleteNews'])->name('news.destroy');

        Route::get('/gallery', [ContentController::class, 'gallery'])->name('gallery.index');
        Route::post('/gallery', [ContentController::class, 'storeGallery'])->name('gallery.store');
        Route::put('/gallery/{galleryItem}', [ContentController::class, 'updateGallery'])->name('gallery.update');
        Route::delete('/gallery/{galleryItem}', [ContentController::class, 'deleteGallery'])->name('gallery.destroy');

        Route::get('/downloads', [ContentController::class, 'downloads'])->name('downloads.index');
        Route::post('/downloads', [ContentController::class, 'storeDownload'])->name('downloads.store');
        Route::put('/downloads/{download}', [ContentController::class, 'updateDownload'])->name('downloads.update');
        Route::delete('/downloads/{download}', [ContentController::class, 'deleteDownload'])->name('downloads.destroy');

        Route::get('/enquiries', [CentralEnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('/enquiries/{enquiry}', [CentralEnquiryController::class, 'show'])->name('enquiries.show');
        Route::patch('/enquiries/{enquiry}', [CentralEnquiryController::class, 'update'])->name('enquiries.update');
        Route::post('/enquiries/{enquiry}/reply', [CentralEnquiryController::class, 'reply'])->name('enquiries.reply');
        Route::post('/enquiries/{enquiry}/follow-up', [CentralEnquiryController::class, 'scheduleFollowUp'])->name('enquiries.follow-up');
        Route::delete('/enquiries/{enquiry}', [CentralEnquiryController::class, 'destroy'])->name('enquiries.destroy');

        Route::get('/auto-replies', [AutoReplyController::class, 'index'])->name('auto-replies.index');
        Route::post('/auto-replies/templates', [AutoReplyController::class, 'storeTemplate'])->name('auto-replies.templates.store');
        Route::post('/auto-replies/rules', [AutoReplyController::class, 'storeRule'])->name('auto-replies.rules.store');
        Route::patch('/auto-replies/rules/{rule}/toggle', [AutoReplyController::class, 'toggleRule'])->name('auto-replies.rules.toggle');
        Route::patch('/auto-replies/business/{institution}/toggle', [AutoReplyController::class, 'toggleBusiness'])->name('auto-replies.business.toggle');

        Route::get('/settings', [ContentController::class, 'settings'])->name('settings.index');
        Route::put('/settings', [ContentController::class, 'saveSettings'])->name('settings.update');
    });
});
