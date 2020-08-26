<?php

namespace App\Http\Controllers;

use App\Jobs\TagImageProcessing;
use App\Tags;

class TagController extends Controller {
    public function scrrapeTagImage() {
        // $tags = Tags::where( 'id', "<", 5001 )->get();
        $tags = Tags::where( 'id', ">", 5000 )->get();
        // $tags = Tags::all();
        // $tags = Tags::limit( 10 )->get();

        // return $tags;

        $tags->map( function ( $tag ) {
            dispatch( new TagImageProcessing( $tag ) );
        } );

    }
}
