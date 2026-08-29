<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstitutionController extends Controller
{
    public function index()
    {
        return view('admin.institutions.index', ['items' => Institution::orderBy('display_order')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['display_order'] = $data['display_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        $data['auto_reply_enabled'] = $request->boolean('auto_reply_enabled');
        $data['sync_enabled'] = $request->boolean('sync_enabled');
        Institution::create($data);
        return back()->with('success', 'Institution added successfully.');
    }

    public function update(Request $request, Institution $institution)
    {
        $data = $this->validated($request);
        if ($institution->name !== $data['name']) $data['slug'] = $this->uniqueSlug($data['name'], $institution->id);
        $data['display_order'] = $data['display_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        $data['auto_reply_enabled'] = $request->boolean('auto_reply_enabled');
        $data['sync_enabled'] = $request->boolean('sync_enabled');
        $institution->update($data);
        return back()->with('success', 'Institution updated successfully.');
    }

    public function generateToken(Institution $institution)
    {
        $plainToken = 'mci_'.Str::random(48);
        $institution->update(['api_token_hash' => hash('sha256', $plainToken), 'sync_enabled' => true]);
        return back()->with('success', 'New API token generated. Copy it now; it will not be shown again.')
            ->with('generated_api_token', $plainToken)
            ->with('generated_api_business', $institution->slug);
    }

    public function destroy(Institution $institution)
    {
        $institution->delete();
        return back()->with('success', 'Institution deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'=>['required','string','max:150'],'website_url'=>['nullable','url','max:255'],'short_description'=>['nullable','string','max:255'],
            'description'=>['nullable','string'],'logo'=>['nullable','string','max:255'],'image'=>['nullable','string','max:255'],
            'display_order'=>['nullable','integer','min:0'],'is_active'=>['nullable','boolean'],'sender_name'=>['nullable','string','max:150'],
            'sender_email'=>['nullable','email','max:150'],'reply_to_email'=>['nullable','email','max:150'],'phone'=>['nullable','string','max:30'],
            'auto_reply_enabled'=>['nullable','boolean'],'sync_enabled'=>['nullable','boolean'],
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'institution'; $slug = $base; $counter = 2;
        while (Institution::query()->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->where('slug',$slug)->exists()) $slug = $base.'-'.$counter++;
        return $slug;
    }
}
