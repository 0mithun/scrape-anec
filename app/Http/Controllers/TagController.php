<?php

namespace App\Http\Controllers;

use App\Tags;
use App\NewTag;
use Goutte\Client;
use App\Jobs\NewTagScraping;
use App\Jobs\RemoveJunkTags;
use App\Jobs\UpdateAmazonLink;
use App\Jobs\TagImageProcessing;
use App\Jobs\RemoveDuplicateItem;
use App\Jobs\InsertOldTagToNewTag;
use Illuminate\Support\Facades\DB;
use App\Jobs\AddBracketsToTagLicense;
use App\Jobs\InserOldThreadTagToNewThreadTag;

class TagController extends Controller
{
    /**
     * @var array
     */
    public $tags = [
        'ted rogers',
        'amazing',
    ];

    public function scrrapeTagImage()
    {
        // $tags = Tags::where( 'id', "<", 5001 )->get();
        $tags = Tags::where('photo', '=', '')->get();

        //5285

        // $tags = Tags::all();

        // $tags = Tags::limit( 10 )->get();

        // return $tags;

        $tags->map(function ($tag) {
            dispatch(new TagImageProcessing($tag));
        });
    }

    public function scrapeTag()
    {

        // $client = new Client();

        foreach ($this->tags as $tag) {
            dispatch(new TagImageProcessing($tag));

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
    public function updatePhotoUrl()
    {
        $tags = Tags::where('photo', 'LIKE', 'public/%')->get();

        // return $tags;

        foreach ($tags as $tag) {
            dispatch(new TagImageProcessing($tag));
        }
    }

    /**
     * @return mixed
     */
    public function updateAmazonLink()
    {

        $tags = Tags::where('description', 'LIKE', '%http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=&%')->get();

        // return $tags;

        foreach ($tags as $tag) {
            dispatch(new UpdateAmazonLink($tag));
        }
    }

    /**
     * @return mixed
     */
    public function removeDuplicate()
    {

        $tags = Tags::where('name', 'LIKE', '% ')->get();

        // return $tags;

        foreach ($tags as $tag) {
            dispatch(new RemoveDuplicateItem($tag));
        }
    }

    /**
     * @return mixed
     */
    public function newTagScrape()
    {
        $tags = Tags::where('photo', 'NOT LIKE', '%download%')->get();
        // $tags = Tags::where('name', 'LIKE', '%pigs%')->get();

        // return $tags;

        //SELECT * FROM `tags` WHERE photo NOT like "%download%"

        foreach ($tags as $tag) {
            dispatch(new NewTagScraping($tag));
        }
    }

    public function removeJunkTags()
    {

        //

        // $tags = Tags::where( 'id', '>', 101032 )->where( 'id', '<', 101566 )->get();
        $tags = Tags::where('id', 101567)->get();

        foreach ($tags as $tag) {
            dispatch(new RemoveJunkTags($tag));
        }
    }

    public function addBracket()
    {

        // $tags = Tags::where('error',1)->get();
        $tags = Tags::where('description', 'NOT LIKE', '%<br>%')->get();
        return $tags;
        foreach ($tags as $tag) {
            \dispatch(new AddBracketsToTagLicense($tag));
        }
    }


    public function insertOldToNewDb()
    {
        $tags = Tags::all();

        foreach ($tags as $tag) {
            dispatch(new InsertOldTagToNewTag($tag));
        }
    }
    public function insertOldThreadTagToNewThreadTag()
    {
        // $tags = DB::table('thread_tag')->where('id', '>', 100000)->where('id', '<=', 150000)->get();
        // $tags = DB::table('thread_tag')->where('id', '>', 150000)->where('id', '<=', 200000)->get();
        // $tags = DB::table('thread_tag')->where('id', '>', 200000)->where('id', '<=', 250000)->get();
        // $tags = DB::table('thread_tag')->where('id', '>', 250000)->where('id', '<=', 300000)->get();
        $tags = DB::table('thread_tag')->where('id', '>', 300000)
            // ->where('id', '<=', 300000)
            ->get();
        // dd($tags);
        foreach ($tags as $tag) {
            dispatch(new InserOldThreadTagToNewThreadTag($tag));
        }
    }
}
