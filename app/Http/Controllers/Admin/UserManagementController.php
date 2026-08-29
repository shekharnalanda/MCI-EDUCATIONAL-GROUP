<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('admin.users.index',[
            'items'=>User::with('institution')->orderBy('name')->get(),
            'institutions'=>Institution::orderBy('display_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $data=$request->validate([
            'name'=>'required|string|max:120','email'=>'required|email|max:180|unique:users,email','password'=>'required|string|min:10|max:255',
            'role'=>'required|in:master_admin,central_manager,business_admin,support_operator','institution_id'=>'nullable|exists:institutions,id','is_active'=>'nullable|boolean',
        ]);
        if(in_array($data['role'],['business_admin','support_operator'],true)&&empty($data['institution_id']))return back()->withErrors(['institution_id'=>'Business assignment is required for this role.'])->withInput();
        $data['is_active']=$request->boolean('is_active'); $user=User::create($data); $audit->record('user.created',$user,[],['role'=>$user->role,'institution_id'=>$user->institution_id]);
        return back()->with('success','Admin user created.');
    }

    public function update(Request $request, User $user, AuditLogger $audit)
    {
        $data=$request->validate([
            'name'=>'required|string|max:120','email'=>['required','email','max:180',Rule::unique('users','email')->ignore($user->id)],
            'role'=>'required|in:master_admin,central_manager,business_admin,support_operator','institution_id'=>'nullable|exists:institutions,id','is_active'=>'nullable|boolean','password'=>'nullable|string|min:10|max:255',
        ]);
        if(in_array($data['role'],['business_admin','support_operator'],true)&&empty($data['institution_id']))return back()->withErrors(['institution_id'=>'Business assignment is required for this role.']);
        if(empty($data['password']))unset($data['password']); $data['is_active']=$request->boolean('is_active');
        if($user->id===auth()->id()&&!$data['is_active'])return back()->withErrors(['user'=>'You cannot deactivate your own account.']);
        $old=$user->only(['name','email','role','institution_id','is_active']); $user->update($data); $audit->record('user.updated',$user,$old,$user->only(['name','email','role','institution_id','is_active']));
        return back()->with('success','Admin user updated.');
    }
}
