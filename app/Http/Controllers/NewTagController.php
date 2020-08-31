<?php

namespace App\Http\Controllers;

use App\NewTag;
use Illuminate\Http\Request;

class NewTagController extends Controller
{
    public function showAllNewTag()
    {
        $tags = NewTag::all();
        $tags =  NewTag::where('task', 'r')->get(); //1025
        // $tags =  NewTag::where('task', 'd')->get(); //7683
        // $tags =  NewTag::where('task', 'a')->get(); //436
        // $tags =  NewTag::where('task', 'w')->get(); //8122
        // $tags =  NewTag::where('task', 'i')->get(); //2106

        return $tags;
    }
}
