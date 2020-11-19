<?php

namespace App\Http\Controllers;

use App\Jobs\CNOJob;
use App\Thread;
use Illuminate\Http\Request;
use DB;

class CnoController extends Controller
{
    //

    public function setCNO()
    {
        // return 'set cno';

        $threads = Thread::all();
        // $threads = Thread::where('id', '<', 25)->limit(10)->get();
        // return $threads;
        foreach ($threads as $thread) {
            dispatch(new CNOJob($thread));
        }
    }

    public function updateCNO()
    {
        $cno = DB::table('cno')->where('cno', 'f')->update(['cno' => 'O']);
    }
}
