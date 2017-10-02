<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pad;

class PadController extends Controller
{
    public function create(Request $r)
    {
    	Pad::create([
    		'user_id' => $r->user()->id,
    		'heading' => $r->newPadHeading,
    		'slug' => md5(uniqid()),
    		'password' => $r->newPadPassword
    	]);
    }

    public function archive(Request $r)
    {
    	$pad = Pad::where('slug', $r->id)->first();
    	if($pad->status == "ACTIVE") {
    		$pad->status = "ARCHIVED";
    	} else {
    		$pad->status = "ACTIVE";
    	}
    	$pad->save();
    }

    public function delete(Request $r)
    {
    	$pad = Pad::where('slug', $r->id)->first();
    	$pad->status = "DELETED";
    	$pad->save();
    }

    public function info($slug)
    {
    	echo json_encode(Pad::where('slug', $slug)->first());
    }
}
