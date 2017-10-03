<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class AcpController extends Controller
{
    public function users(Request $r)
    {
    	$this->checkPermissions($r->user());

    	return view('acp.users');
    }

    public function logs()
    {
    	
    }

    public function trash()
    {
    	
    }

    public function userInfo(Request $r, User $id)
    {
    	$this->checkPermissions($r->user());

    	echo json_encode($id);
    }

    public function userEdit(Request $r, $id)
    {
		$this->checkPermissions($r->user());

    	$user = User::findOrFail($id);
    	$user->name = $r->name;
    	$user->email = $r->email;
    	$user->status = $r->status;
    	$user->save();
    }

    private function checkPermissions(User $user)
    {
    	if($user->status != "ADMIN") {
    		return redirect()->back()->with('error', 'Missing permissions!');
    	}
    }
}
