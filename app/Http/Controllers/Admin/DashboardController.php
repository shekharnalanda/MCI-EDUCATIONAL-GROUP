<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Enquiry;
use App\Models\GalleryItem;
use App\Models\Institution;
use App\Models\NewsPost;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'institutionCount' => Institution::count(),
            'newsCount' => NewsPost::count(),
            'galleryCount' => GalleryItem::count(),
            'downloadCount' => Download::count(),
            'enquiryCount' => Enquiry::count(),
        ]);
    }
}
