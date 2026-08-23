<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Enquiry;
use App\Models\GalleryItem;
use App\Models\NewsPost;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function news() { return view('admin.content.news', ['items' => NewsPost::latest('published_at')->latest()->get()]); }
    public function storeNews(Request $request)
    {
        $data = $request->validate(['title'=>'required|max:180','excerpt'=>'nullable','content'=>'nullable','published_at'=>'nullable|date','is_active'=>'nullable|boolean']);
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(4));
        $data['is_active'] = $request->boolean('is_active');
        NewsPost::create($data);
        return back()->with('success','News/Event added.');
    }
    public function updateNews(Request $request, NewsPost $newsPost)
    {
        $data = $request->validate(['title'=>'required|max:180','excerpt'=>'nullable','content'=>'nullable','published_at'=>'nullable|date','is_active'=>'nullable|boolean']);
        $data['is_active'] = $request->boolean('is_active');
        $newsPost->update($data);
        return back()->with('success','News/Event updated.');
    }
    public function deleteNews(NewsPost $newsPost) { $newsPost->delete(); return back()->with('success','News/Event deleted.'); }

    public function gallery() { return view('admin.content.gallery', ['items' => GalleryItem::orderBy('display_order')->latest()->get()]); }
    public function storeGallery(Request $request)
    {
        $data = $request->validate(['title'=>'required|max:180','image'=>'required|max:255','caption'=>'nullable','display_order'=>'nullable|integer|min:0','is_active'=>'nullable|boolean']);
        $data['is_active'] = $request->boolean('is_active');
        GalleryItem::create($data);
        return back()->with('success','Gallery item added.');
    }
    public function updateGallery(Request $request, GalleryItem $galleryItem)
    {
        $data = $request->validate(['title'=>'required|max:180','image'=>'required|max:255','caption'=>'nullable','display_order'=>'nullable|integer|min:0','is_active'=>'nullable|boolean']);
        $data['is_active'] = $request->boolean('is_active');
        $galleryItem->update($data);
        return back()->with('success','Gallery item updated.');
    }
    public function deleteGallery(GalleryItem $galleryItem) { $galleryItem->delete(); return back()->with('success','Gallery item deleted.'); }

    public function downloads() { return view('admin.content.downloads', ['items' => Download::orderBy('display_order')->latest()->get()]); }
    public function storeDownload(Request $request)
    {
        $data = $request->validate(['title'=>'required|max:180','description'=>'nullable','file_path'=>'nullable|max:255','external_url'=>'nullable|url|max:255','display_order'=>'nullable|integer|min:0','is_active'=>'nullable|boolean']);
        $data['is_active'] = $request->boolean('is_active');
        Download::create($data);
        return back()->with('success','Download added.');
    }
    public function updateDownload(Request $request, Download $download)
    {
        $data = $request->validate(['title'=>'required|max:180','description'=>'nullable','file_path'=>'nullable|max:255','external_url'=>'nullable|url|max:255','display_order'=>'nullable|integer|min:0','is_active'=>'nullable|boolean']);
        $data['is_active'] = $request->boolean('is_active');
        $download->update($data);
        return back()->with('success','Download updated.');
    }
    public function deleteDownload(Download $download) { $download->delete(); return back()->with('success','Download deleted.'); }

    public function enquiries() { return view('admin.content.enquiries', ['items' => Enquiry::latest()->get()]); }
    public function updateEnquiry(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate(['status'=>'required|in:new,contacted,closed']);
        $enquiry->update($data);
        return back()->with('success','Enquiry status updated.');
    }
    public function deleteEnquiry(Enquiry $enquiry) { $enquiry->delete(); return back()->with('success','Enquiry deleted.'); }

    public function settings() { return view('admin.content.settings', ['items' => Setting::orderBy('key')->get()]); }
    public function saveSettings(Request $request)
    {
        foreach ($request->input('settings', []) as $key => $value) {
            Setting::updateOrCreate(['key'=>$key], ['value'=>$value]);
        }
        return back()->with('success','Website settings updated.');
    }
}
