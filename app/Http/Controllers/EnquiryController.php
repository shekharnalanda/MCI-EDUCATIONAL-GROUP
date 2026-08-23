<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $data['status'] = 'new';
        Enquiry::create($data);

        return back()->with('success', 'Thank you. Your enquiry has been submitted successfully.');
    }
}
