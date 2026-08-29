<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query=AuditLog::with(['user','institution'])->latest();
        if($request->filled('action'))$query->where('action','like','%'.trim((string)$request->action).'%');
        if($request->filled('user_id'))$query->where('user_id',$request->integer('user_id'));
        return view('admin.audit.index',['items'=>$query->paginate(40)->withQueryString()]);
    }
}
