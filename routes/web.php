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
