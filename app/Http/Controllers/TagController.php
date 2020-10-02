<?php

namespace App\Http\Controllers;

use App\Jobs\TagImageProcessing;
use App\Jobs\UpdateAmazonLink;
use App\NewTag;
use App\Tags;
use Goutte\Client;
use phpDocumentor\Reflection\DocBlock\Tag;

class TagController extends Controller {
    /**
     * @var array
     */
    public $tags = [
        'ted rogers',
        'amazing',
    ];

    public function scrrapeTagImage() {
        // $tags = Tags::where( 'id', "<", 5001 )->get();
        $tags = Tags::where( 'photo', '=', '' )->get();

//5285

// $tags = Tags::all();

// $tags = Tags::limit( 10 )->get();

        // return $tags;

        $tags->map( function ( $tag ) {
            dispatch( new TagImageProcessing( $tag ) );
        } );
    }

    public function scrapeTag() {

// $client = new Client();

        foreach ( $this->tags as $tag ) {
            dispatch( new TagImageProcessing( $tag ) );

// $url = 'https://en.wikipedia.org/wiki/' . $tag;

// $crawler = $client->request('GET', $url);

// $anchor =  $crawler->filter('div.thumbinner a.image')->first();

// $authorText = '';

// $licenseText = '';

// $descriptionText = '';

// $shopText = '';

// if (count($anchor) > 0) {

//     $href = $anchor->extract(['href'])[0];

//     $image_page_url = 'https://en.wikipedia.org' . $href;

//     $image_page = $client->request('GET', $image_page_url);

//     $full_image_link =  $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];

//     $full_image_link = str_replace('//upload', 'upload', $full_image_link);

//     $full_image_link = 'https://' . $full_image_link;

//     $description = $image_page->filter('td.description');

//     $description = ($description->count() > 0) ? $description->first()->text() : "";

//     $descriptionText = str_replace('English: ', '', $description);

//     $license = $image_page->filter('table.licensetpl span.licensetpl_short');

//     $licenseText = ($license->count() > 0) ? $license->first()->text() : "";

//     $author = $image_page->filter('td#fileinfotpl_aut');

//     // $author = ($author->count() > 0) ? $author->first()->text() : '';

//     if ($author->count() > 0) {

//         $newAuthor = $image_page->filter('td#fileinfotpl_aut')->nextAll();

//         $newAuthor = $newAuthor->filter('a');

//         $authorText =  $newAuthor->first()->text();

//     }

//     dump($full_image_link);

//     dump($descriptionText);

//     dump($licenseText);

//     dump($authorText);
            // }
        }

    }

    /**
     * @return mixed
     */
    public function updatePhotoUrl() {
        $tags = Tags::where( 'photo', 'LIKE', 'public/%' )->get();

// return $tags;

        foreach ( $tags as $tag ) {
            dispatch( new TagImageProcessing( $tag ) );
        }

    }

    /**
     * @return mixed
     */
    public function updateAmazonLink() {

        $tags = Tags::where( 'description', 'LIKE', '%http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=&%' )->get();

// return $tags;

        foreach ( $tags as $tag ) {
            dispatch( new UpdateAmazonLink( $tag ) );
        }

    }

}
