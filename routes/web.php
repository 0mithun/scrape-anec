<?php

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

Route::get('/', function () {
    return view('welcome');
});

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
// Route::get( '/scrape-tags', 'TagController@scrrapeTagImage' );

// Route::get('/set-amazon-pixel', 'ScrapeController@amaxonpixel');
// Route::get('/set-other-pixel', 'ScrapeController@otherpixel');
// Route::get('/set-wiki-pixel', 'ScrapeController@wikipixel');

// Route::get('/tag-scrape', 'TagController@scrapeTag');

Route::get('/new-tag', 'NewTagController@showAllNewTag');


Route::get('/test', function () {
    $string = '{"uuid":"c10ad2ad-c595-490b-af40-d10b9c56c801","displayName":"App\\Jobs\\TagImageProcessing","job":"Illuminate\\Queue\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"delay":null,"timeout":null,"timeoutAt":null,"data":{"commandName":"App\\Jobs\\TagImageProcessing","command":"O:27:\"App\\Jobs\\TagImageProcessing\":9:{s:6:\"\u0000*\u0000tag\";O:45:\"Illuminate\\Contracts\\Database\\ModelIdentifier\":4:{s:5:\"class\";s:10:\"App\\NewTag\";s:2:\"id\";i:6;s:9:\"relations\";a:0:{}s:10:\"connection\";s:5:\"mysql\";}s:3:\"job\";N;s:10:\"connection\";N;s:5:\"queue\";N;s:15:\"chainConnection\";N;s:10:\"chainQueue\";N;s:5:\"delay\";N;s:10:\"middleware\";a:0:{}s:7:\"chained\";a:0:{}}"}}';
    $decode = (json_decode(json_encode($string)));

    return $decode;
});
