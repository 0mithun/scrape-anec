<?php

use App\Http\Controllers\ThreadController;
use App\Notifications\TestEmailNotification;
use App\Tags;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get( '/', function () {
    return view( 'welcome' );
} );

// Route::get( '/update-slug', 'ThreadController@updateSlug' );

// Route::get( '/update-word-count', 'ThreadController@updateWordCount' );

// Route::get( '/get-source-location', 'ThreadController@getSourceLocation' );

// Route::get( '/get-source', 'ThreadController@getSource' );

// Route::get( '/get-location', 'ThreadController@getLocation' );

// Route::get( '/update-source', 'ThreadController@updateSource' );

// Route::get( '/update-location', 'ThreadController@updateLocation' );

// Route::get( '/update-cno', 'ThreadController@updateCNO' );

// Route::get( '/update-created-at', 'ThreadController@updateCreatedAt' );

// Route::get( '/update-channel', 'ThreadController@updateChannel' );

// Route::get( '/add-channel-to-tags', 'ThreadController@addChannelToTags' );

// Route::get( '/attach-tags', 'ThreadController@attachTags' );

// Route::get( '/attach-channel-tags', 'ThreadController@attachChannelTags' );

// Route::get( '/remove-duplicate-title', 'ThreadController@removeDuplcateTitle' );

// Route::get( '/scrape-info', 'ScrapeController@scrape' );

// Route::get( '/scrape-amazon', 'ScrapeController@scrapeAmazon' );

// Route::get( '/scrape-image', 'ScrapeController@scrapeImage' );

// Route::get( '/check-html', 'ScrapeController@checkHTML' );

// Route::get( '/check-image-url', 'ScrapeController@checkImageURl' );

// Route::get( '/set-location', 'ScrapeController@setLocation' );

// Route::get( '/remove-public', 'ScrapeController@removePublicFromImagePath' );

//Incomplete

// Route::get('/scrape-tags', 'TagController@scrrapeTagImage');

// Route::get('/set-amazon-pixel', 'ScrapeController@amaxonpixel');

// Route::get('/set-other-pixel', 'ScrapeController@otherpixel');

Route::get('/set-wiki-pixel', 'ScrapeController@wikipixel');

// Route::get('/tag-scrape', 'TagController@scrapeTag');

// Route::get('/new-tag', 'NewTagController@showAllNewTag');

// Route::

Route::get( '/test', function () {

// return Tags::where('photo', '!=', '')->get();

// return Tags::where('photo', 'LIKE', '%.com%')->get();

// return Tags::where('updated_at', '>', now()->subHour(5))->get();

// $user = User::first();

    // $user->notify(new TestEmailNotification);

    $threads = Thread::where( 'wiki_image_path', null )->Where( 'amazon_image_path', '=', null )->Where( 'other_image_path', '=', null )->limit( 100 )->get();

// return $threads;

// $threads = Thread::where('wiki_image_path', '!=', '')->count(); //27972

// $threads = Thread::where('amazon_image_path', '!=', '')->count(); //696

// $threads = Thread::where('other_image_path', '!=', '')->get(); //968

// file_put_contents('data.csv', collect($threads)->values()->all());
    // return $threads; //29636//39569

    $fileName = 'data.csv';

    $headers = array(
        'Content-type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=$fileName",
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0',
    );

    $columns = array(
        'id',
        'slug',
        'user_id',
        'channel_id',
        'title',
        'body',
        'summary',
        'source',
        'main_subject',
        'image_path',
        'image_path_pixel_color',
        'location',
        'lat',
        'lng',
        'cno',
        'age_restriction',
        'anonymous',
        'wiki_info_page_url',
        'wiki_image_description',
        'wiki_image_page_url',
        'wiki_image_url',
        'wiki_image_path',
        'wiki_image_path_pixel_color',
        'amazon_product_url',
        'amazon_image_url',
        'amazon_image_path',
        'amazon_image_path_pixel_color',
        'other_image_url',
        'other_image_path',
        'other_image_path_pixel_color',
        'description',
        'image_saved',
        'word_count',
        'replies_count',
        'like_count',
        'dislike_count',
        'favorite_count',
        'visits',
        'average_rating',
        'is_published',
        'famous',
        'slide_body',
        'slide_image_pos',
        'slide_color_bg',
        'slide_color_0',
        'slide_color_1',
        'slide_color_2',
        'photo_desc',
        'error',
        'created_at',
        'updated_at',
    );

    $callback = function () use ( $threads, $columns ) {
        $file = fopen( 'php://output', 'w' );
        fputcsv( $file, $columns );

        foreach ( $threads as $task ) {

// $row['Title']  = $task->title;

// $row['Assign']    = $task->assign->name;

// $row['Description']    = $task->description;

// $row['Start Date']  = $task->start_at;

// $row['Due Date']  = $task->end_at;

            // fputcsv($file, array($row['Title'], $row['Assign'], $row['Description'], $row['Start Date'], $row['Due Date']));
            fputcsv( $file, $task->toArray() );
        }

        fclose( $file );
    };

    return response()->stream( $callback, 200, $headers );
} );

// Route::get( 'update-photo', 'TagController@updatePhotoUrl' );

// Route::get('/set-cno', 'CnoController@setCNO');

// Route::get('/update-cno', 'CnoController@updateCNO');

// Route::get('/update-amazon-link', 'ThreadController@updateAmazonLink');

// Route::get( '/update-tag-names', 'ThreadController@updateTagNameList' );

// Route::get( '/extract-i', 'ThreadController@replaceAmazonLink' );

// Route::get('/upvote', 'ThreadController@upvote');

// Route::get( '/update-amazon-link', 'TagController@updateAmazonLink' );

Route::get( 'strip-slug-tags', 'ThreadController@stripSlug' );

// Route::get( '/remove-duplicate-tags', 'TagController@removeDuplicate' );

// Route::get( '/remove-duplicate-tag-id', 'ThreadController@removeDuplicateTagId' )

// Route::get( '/new-wiki-scrape', 'ThreadController@newWikiScrape' );

Route::get( '/image-page-not-found', function () {
    return DB::table( 'image_page_not_found' )->get();
} );

// Route::get( 'new-tag-scrape', 'TagController@newTagScrape' );

// Route::get( '/remove-junk-tags', 'TagController@removeJunkTags' );

// Route::get('/tag-html-descriptoin','TagController@tagHtmlDescription');

// Route::get('/replace-source','ThreadController@replaceSource');

Route::get('/replace-first-p','ThreadController@replaceFirstP');



Route::get('/scrape-thread-image-with-list','ThreadController@scrapeImageWithName');

// Route::get('/update-wiki-description','ThreadController@updateWikiDescription');

// Route::get('/update-null-location','ThreadController@updateNullLocation');

// Route::get('rescrape-description','ThreadController@reScrapeDescription');

Route::get('thread/update-amazon-link','ThreadController@upadteAmazonLink');

Route::get('add-bracket-to-tag-license','TagController@addBracket');