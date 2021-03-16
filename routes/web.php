<?php

use App\Http\Controllers\ThreadController;
use App\Jobs\AddOldLikeToNewLike;
use App\Jobs\TestScrapingJob;
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

Route::get('/test-size', function () {
    // Assume failure.
  $result = -1;

  $url = 'https://upload.wikimedia.org/wikipedia/commons/d/d0/Dean_Franklin_-_06.04.03_Mount_Rushmore_Monument_%28by-sa%29.jpg';
  $url = 'https://anecdotage.com/anecdotes/eleanor-of-aquitaine?fbclid=IwAR10RkXO_UpyICX9yOFws5N9HxEJ3JlxgAyQg1T0ZKa_i6xTFKTLQ6uGbsM';
 $ch = curl_init($url);

     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
     curl_setopt($ch, CURLOPT_HEADER, TRUE);
     curl_setopt($ch, CURLOPT_NOBODY, TRUE);

     $data = curl_exec($ch);
     $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

     curl_close($ch);
     return $size;



    // $url = 'http://stackoverflow.com/questions/2602612/php-remote-file-size-without-downloading-file';

    // $headers = get_headers($url, true);

    // if ( isset($headers['Content-Length']) ) {
    // $size = 'file size:' . $headers['Content-Length'];
    // }
    // else {
    // $size = 'file size: unknown';
    // }

    // echo $size;
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

// Route::get('/scrape-tags', 'TagController@scrrapeTagImage');

// Route::get('/set-amazon-pixel', 'ScrapeController@amaxonpixel');

// Route::get('/set-other-pixel', 'ScrapeController@otherpixel');

Route::get('/set-wiki-pixel', 'ScrapeController@wikipixel');

// Route::get('/tag-scrape', 'TagController@scrapeTag');

// Route::get('/new-tag', 'NewTagController@showAllNewTag');

// Route::

Route::get('/test', function () {

    // return Tags::where('photo', '!=', '')->get();

    // return Tags::where('photo', 'LIKE', '%.com%')->get();

    // return Tags::where('updated_at', '>', now()->subHour(5))->get();

    // $user = User::first();

    // $user->notify(new TestEmailNotification);

    $threads = Thread::where('wiki_image_path', null)->Where('amazon_image_path', '=', null)->Where('other_image_path', '=', null)->limit(100)->get();

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

    $callback = function () use ($threads, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($threads as $task) {

            // $row['Title']  = $task->title;

            // $row['Assign']    = $task->assign->name;

            // $row['Description']    = $task->description;

            // $row['Start Date']  = $task->start_at;

            // $row['Due Date']  = $task->end_at;

            // fputcsv($file, array($row['Title'], $row['Assign'], $row['Description'], $row['Start Date'], $row['Due Date']));
            fputcsv($file, $task->toArray());
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
});

// Route::get( 'update-photo', 'TagController@updatePhotoUrl' );

// Route::get('/set-cno', 'CnoController@setCNO');

// Route::get('/update-cno', 'CnoController@updateCNO');

// Route::get('/update-amazon-link', 'ThreadController@updateAmazonLink');

// Route::get( '/update-tag-names', 'ThreadController@updateTagNameList' );

// Route::get( '/extract-i', 'ThreadController@replaceAmazonLink' );

// Route::get('/upvote', 'ThreadController@upvote');

// Route::get( '/update-amazon-link', 'TagController@updateAmazonLink' );

Route::get('strip-slug-tags', 'ThreadController@stripSlug');

// Route::get( '/remove-duplicate-tags', 'TagController@removeDuplicate' );

// Route::get( '/remove-duplicate-tag-id', 'ThreadController@removeDuplicateTagId' )

// Route::get( '/new-wiki-scrape', 'ThreadController@newWikiScrape' );

Route::get('/image-page-not-found', function () {
    return DB::table('image_page_not_found')->get();
});

Route::get( 'new-tag-scrape', 'TagController@newTagScrape' );

// Route::get( '/remove-junk-tags', 'TagController@removeJunkTags' );

// Route::get('/tag-html-descriptoin','TagController@tagHtmlDescription');

// Route::get('/replace-source','ThreadController@replaceSource');

Route::get('/replace-first-p', 'ThreadController@replaceFirstP');



Route::get('/scrape-thread-image-with-list', 'ThreadController@scrapeImageWithName');

// Route::get('/update-wiki-description','ThreadController@updateWikiDescription');

// Route::get('/update-null-location','ThreadController@updateNullLocation');

// Route::get('rescrape-description','ThreadController@reScrapeDescription');

Route::get('thread/update-amazon-link', 'ThreadController@upadteAmazonLink');

Route::get('add-bracket-to-tag-license', 'TagController@addBracket');


Route::get('add-bracket-to-thread-license', 'ThreadController@addBracket');

// Route::get('insert-amzon-product-url-to-threads-table', 'ThreadController@insertAmazonProductUrlToThreadsTable');

// Route::get('new-namelist-scraping', 'ThreadController@newNameListScraping');
Route::get('insert-missing-author', 'ThreadController@insertMissingJob');

// Route::get('insert-old-to-new-db', 'ThreadController@insertOldToNewDb');
Route::get('insert-old-tag-to-new-tag', 'TagController@insertOldToNewDb');

// Route::get('insert-old-thread-tag-to-new-thread-tag', 'TagController@insertOldThreadTagToNewThreadTag');


// Route::get('add-old-like-to-new-like', function () {
//     $likes = \DB::table('likes')->get();
//     foreach ($likes as $like) {
//         dispatch(new AddOldLikeToNewLike($like));
//     }
// });

Route::get('/test', function () {
    $urls = [
        'https://commons.wikimedia.org/wiki/File:Open_Happiness_Piccadilly_Circus_Blue-Pink_Hour_120917-1126-jikatu.jpg',
        'https://commons.wikimedia.org/wiki/File:ChevyChaseMar10.jpg',
        'https://commons.wikimedia.org/wiki/File:Dean_Cain_Brussels_Comic_Con_2018_(cropped).jpg',
        'https://en.wikipedia.org/wiki/File:Matchbox_twenty_in_MAA_03.jpg',
        'https://en.wikipedia.org/wiki/File:Vince_McMahon_in_2016.jpg',
        'https://en.wikipedia.org/wiki/ZZ_Top',
        'https://en.wikipedia.org/wiki/Zsa_Zsa_Gabor',
        'https://en.wikipedia.org/wiki/Sidney_Sheldon',
        'https://en.wikipedia.org/wiki/Arnold_Schwarzenegger',
        'https://en.wikipedia.org/wiki/Zsa_Zsa_Gabor',
        'https://en.wikiquote.org/wiki/Venus_(mythology)',
        'https://en.wikipedia.org/wiki/Alice_Cooper',
        'https://en.wikipedia.org/wiki/Rob_Zombie',
        'https://en.wikipedia.org/wiki/Cher',
        'https://en.wikipedia.org/wiki/Ziggy_Marley',
        'https://en.wikipedia.org/wiki/Zeuxis',
        'https://en.wikipedia.org/wiki/File:Zero_Mostel_-_Fiddler.JPG',
        'https://en.wikipedia.org/wiki/File:Zell_B_Miller.jpg',
        'https://en.wikipedia.org/wiki/File:Zbigniew_Rybczy%C5%84sk_at_The_Cinefamily.jpg',
        'https://en.wikipedia.org/wiki/File:Zappa_16011977_01_300.jpg',
        'https://en.wikipedia.org/wiki/File:Zahn,_Steve_(2008).jpg',
    ];

    //  foreach ($urls as $url) {
    //     dispatch(new TestScrapingJob($url));
    // }


$threads = array(
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Mae_West_LAT.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Retrato_de_Julio_C%C3%A9sar_(26724093101)_(cropped).jpg'),

  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:PT_Barnum_1851-crop.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Sir_Isaac_Newton_(1643-1727).jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Harry_Cohn_Oscar_1938_cropped.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Aspettando_Clint_Keith_Carradine_fcm.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Fred_Astaire_1962.JPG'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Thom_Yorke_Austin_Texas_2016_(cropped).jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:LeBron_James_crop.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:AlfredSmith.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Wolfgang_Puck_2012.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Grover_Cleveland,_by_Frederick_Gutekunst.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Muhammad_Ali_NYWTS.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Grover_Cleveland_-_NARA_-_518139.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Michael_Douglas_C%C3%A9sar_2016_3.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Richard_Widmark_-_1973.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Alexander_Pope_by_Michael_Dahl.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Wendy_Liebman.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Val_Kilmer_Cannes.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Gloria_Estefan_in_2017.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Kim_Basinger_(1990).jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Mr_T_WWE_Hall_of_Fame_2014_(cropped).jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Dennis_Wilson_1971_cropped.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:George_Michael_(2011).jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Laurette_Taylor.jpg'),

  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Ice-Cube_2014-01-09-Chicago-photoby-Adam-Bielawski.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Man_Ray,_1920-21,_Portrait_of_Marcel_Duchamp,_gelatin_silver_print,_Yale_University_Art_Gallery.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Hank_Williams_Promotional_Photo.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Portrett_av_Edvard_Munch_(cropped).jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Bundesarchiv_Bild_183-76052-0335,_Schacholympiade,_Tal_(UdSSR)_gegen_Fischer_(USA)_Crop.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Hans_Holbein,_the_Younger_-_Sir_Thomas_More_-_Google_Art_Project.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Jeffrey_Archer_@_Oslo_bokfestival_2012_4.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:John_Bright.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Anthony_bourdain_peabody_2014b.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Domenico_Maria_Canuti_-_Alexander_and_Bucephalus.jpeg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Nikki_Sixx.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Kings_of_Leon_Hyde_Park.jpg'),
  array('wiki_image_page_url' => 'https://en.wikipedia.org/wiki/File:Steven_Wright_1994.jpg'),
);

 foreach ($threads as $url) {
        dispatch(new TestScrapingJob($url['wiki_image_page_url']));
    }



});

