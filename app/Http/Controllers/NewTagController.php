<?php

namespace App\Http\Controllers;

use App\NewTag;
use App\Jobs\TagImageProcessing;

class NewTagController extends Controller
{
    /**
     * @return mixed
     */
    public function showAllNewTag()
    {
        //Thread tags 313190
        //tags 15421

        /**
         * Running Sequence (d, r, a then scrape from tags table then task i, w)
         * Task I need run after all
         */
        // $tags = NewTag::all();

        // $tags =  NewTag::where('task', 'r')->get(); //1025//Run Complete

        // $tags =  NewTag::where('task', 'd')->get(); //7683//Run Completed

        // $tags =  NewTag::where('task', 'a')->get(); //436 //Run Completed
        // $tags = NewTag::where('task', 'w')->limit(2000)->get();
        //8122

        $tags =  NewTag::where('task', 'w')->get(); //8122
        // $tags =  NewTag::where('task', 'w')->limit(100)->get(); //8122
        // $tags =  NewTag::where('task', 'i')->where('amazon_product_link', '=', '')->get(); //2106

        // return $tags;

        foreach ($tags as $tag) {
            dispatch(new TagImageProcessing($tag));
        }
    }
}
