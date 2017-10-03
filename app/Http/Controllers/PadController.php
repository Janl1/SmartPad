<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pad;

class PadController extends Controller
{
    public function create(Request $r)
    {
        if($r->newPadPassword != "NULL") {
            $r->newPadPassword = sha1($r->newPadPassword);
        }
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

    public function password(Request $r, $slug)
    {   
        $pad = Pad::where('slug', $slug)->first();

        if($r->isMethod('post')) {

            if(sha1($r->password) == $pad->password) {
                $pad->clicks = $pad->clicks + 1;
                $pad->save();
                return view('pads.show')->with('pad', $pad);
            } else {
                return redirect()->back()->with('error', 'Wrong password!');
            }

        } else {
            return view('pads.password')->with('pad', $pad);
        }
    }

    public function info($slug)
    {
    	echo json_encode(Pad::where('slug', $slug)->first());
    }

    public function open($slug)
    {
        $pad = Pad::where('slug', $slug)->first();

        if($pad->password != "NULL") {
            return redirect(url('/pad/password/' . $slug));
        }

        $pad->clicks = $pad->clicks + 1;
        $pad->save();

        return view('pads.show')->with('pad', $pad);
    }
}
