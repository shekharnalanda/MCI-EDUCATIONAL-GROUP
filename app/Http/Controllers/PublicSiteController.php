<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\GalleryItem;
use App\Models\Institution;
use App\Models\NewsPost;
use App\Models\Setting;

class PublicSiteController extends Controller
{
    private function settings(): array
    {
        return Setting::query()->pluck('value', 'key')->all();
    }

    public function home()
    {
        return view('home', [
            'institutions' => Institution::where('is_active', true)->orderBy('display_order')->orderBy('name')->get(),
            'newsItems' => NewsPost::where('is_active', true)->orderByDesc('published_at')->orderByDesc('id')->limit(6)->get(),
            'galleryItems' => GalleryItem::where('is_active', true)->orderBy('display_order')->orderByDesc('id')->limit(8)->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function institutions()
    {
        return view('institutions', [
            'institutions' => Institution::where('is_active', true)->orderBy('display_order')->orderBy('name')->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function news()
    {
        return view('news', [
            'items' => NewsPost::where('is_active', true)->orderByDesc('published_at')->orderByDesc('id')->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function gallery()
    {
        return view('gallery', [
            'items' => GalleryItem::where('is_active', true)->orderBy('display_order')->orderByDesc('id')->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function downloads()
    {
        return view('downloads', [
            'items' => Download::where('is_active', true)->orderBy('display_order')->orderByDesc('id')->get(),
            'settings' => $this->settings(),
        ]);
    }
}
