<?php

namespace App\Http\Controllers;

use App\Thread;
use DB;
use Goutte\Client;
use Illuminate\Http\Request;

class ThreadController extends Controller {
    public $amazonURLS = [
        "https://www.amazon.com/Friends-Television-Soundtrack/dp/B000002N1S",
        "https://www.amazon.com/What-Every-BODY-Saying-Speed-Reading/dp/0061438294",
        "https://www.amazon.com/Lloyd-Ulyate-His-Trombone/dp/B00E4794FC",
        "https://www.amazon.com/Going-Own-Way-Gary-Crosby/dp/0385170556/"
        , "https://www.amazon.com/Mennonite-Little-Black-Dress-Memoir/dp/0805092250/",
        "https://www.amazon.com/Giovane-Toscanini-Franco-Zeffirelli/dp/B000YFDP9O",
        "https://images-na.ssl-images-amazon.com/images/I/51i9kU9pkmL._SX332_BO1,204,203,200_.jpg"
        , "https://www.amazon.com/Strangers-Night-Frank-Sinatra/dp/B000002K9M"
        , "https://www.amazon.com/Walk-Hard-Dewey-Two-Disc-Special/dp/B0012IWNZY/",

    ];
    /**
     * All Slug update complete
     */
    public function updateSlug( Request $request ) {
        // $threads = Thread::where( 'id', '<', 5000 )
        //     ->get();

        $threads = Thread::where( 'id', '>', 120000 )
            ->where( 'id', '<', 140000 )
            ->get();
        dd( $threads );

        $threads->map( function ( $thread ) {
            // $thread->update();

            if ( $thread->whereSlug( $slug = str_slug( $thread->title ) )->exists() ) {
                $slug = "{$slug}-{$thread->id}";
            } else {
                $slug = str_slug( $thread->title );
            }
            $thread->update( ['slug' => $slug] );

        } );

        return 'Update';
    }

    /**
     * All Slug update complete
     */
    public function updateWordCount( Request $request ) {
        // $threads = Thread::where( 'id', '<', 5000 )
        //     ->get();

        $threads = Thread::where( 'id', '>', 120000 )
            ->where( 'id', '<', 150000 )
            ->get();
        dd( $threads );

        $threads->map( function ( $thread ) {
            $thread->update( ['word_count' => str_word_count( $thread->body )] );

        } );

        return 'Update';
    }

    /**
     *  get all threads source & location value
     */
    public function getSourceLocation( Request $request ) {
        $data = DB::table( 'engine4_article_fields_values' )->select( '*' )->where( 'field_id', 1 )->get();
        dd( $data );

        // $threads = Thread::where( 'id', '<', 5000 )
        //     ->get();

        // $threads = Thread::where( 'id', '>', 120000 )
        //     ->where( 'id', '<', 150000 )
        //     ->get();
        // dd( $threads );

        // $threads->map( function ( $thread ) {
        //     $thread->update( ['word_count' => str_word_count( $thread->body )] );

        // } );

        //location 574 /field_id = 2
        //source 30231 /field_id = 1

        return 'Update';
    }

    /**
     *  get all threads source & location value
     */
    public function getSource( Request $request ) {
        $data = Thread::where( 'source', '!=', '' )->get();
        // $data = Thread::where( 'location', '!=', '' )->get();
        dump( $data );
        // return $data;

        // $threads = Thread::where( 'id', '<', 5000 )
        //     ->get();

        // $threads = Thread::where( 'id', '>', 120000 )
        //     ->where( 'id', '<', 150000 )
        //     ->get();
        // dd( $threads );

        // $threads->map( function ( $thread ) {
        //     $thread->update( ['word_count' => str_word_count( $thread->body )] );

        // } );

        //location 1642
        //source 9612

        return 'Update';
    }

    /**
     *  get all threads source & location value
     */
    public function getLocation( Request $request ) {
        $data = Thread::where( 'location', '!=', '' )->get();
        // $data = Thread::where( 'location', '!=', '' )->get();
        dump( $data );
        // return $data;

        // $threads = Thread::where( 'id', '<', 5000 )
        //     ->get();

        // $threads = Thread::where( 'id', '>', 120000 )
        //     ->where( 'id', '<', 150000 )
        //     ->get();
        // dd( $threads );

        // $threads->map( function ( $thread ) {
        //     $thread->update( ['word_count' => str_word_count( $thread->body )] );

        // } );

        //location 1642
        //source 9612

        return 'Update';
    }

    /**
     * Update source
     */

    public function updateSource( Request $request ) {
        // $data = DB::table( 'engine4_article_fields_values' )->select( '*' )->where( 'field_id', 1 )->where( 'item_id', '<', 5000 )->get();
        $data = DB::table( 'engine4_article_fields_values' )
            ->select( '*' )
            ->where( 'field_id', 1 )
            ->where( 'item_id', '<', 150000 )
            ->where( 'item_id', '>', 118999 )
            ->get();
        // dd( $data );
        $data->map( function ( $item ) {
            // $thread->update( ['word_count' => str_word_count( $thread->body )] );
            $thread = Thread::where( 'id', $item->item_id )->first();
            if ( $thread ) {
                if ( $thread->source == '' ) {
                    $thread->source = $item->value;
                    $thread->save();
                }
            }

        } );

        return 'Source Update complete';
    }

    /**
     * Update location
     */

    public function updateLocation( Request $request ) {
        // $data = DB::table( 'engine4_article_fields_values' )->select( '*' )->where( 'field_id', 1 )->where( 'item_id', '<', 5000 )->get();
        $data = DB::table( 'engine4_article_fields_values' )
            ->select( '*' )
            ->where( 'field_id', 2 )
        // ->where( 'item_id', '<', 150000 )
        // ->where( 'item_id', '>', 118999 )
            ->get();
        // dd( $data );
        $data->map( function ( $item ) {
            // $thread->update( ['word_count' => str_word_count( $thread->body )] );
            $thread = Thread::where( 'id', $item->item_id )->first();
            if ( $thread ) {
                if ( $thread->location == '' ) {
                    $thread->location = $item->value;
                    $thread->save();
                }
            }

        } );

        return 'Location Update complete';
    }

    /**
     * Update cno
     */

    public function updateCNO( Request $request ) {
        $threads = Thread::where( 'id', '>', '119999' )->where( 'id', '<', 150000 )->update( [
            'CNO' => 'N',
        ] );

        return 'CNO Update complete';
    }

    /**
     * Update created_at
     */

    public function updateCreatedAt( Request $request ) {
        Thread::where( 'id', '>', 0 )->update( [
            'created_at' => now(),
        ] );

        return 'CNO Update complete';
    }

    /**
     * get Tags
     */

    public function updateChannel( Request $request ) {
        $threads = Thread::where( 'tags', '=', NULL )->where( 'id', '>', 19999 )->get();
        // dd( $threads );
        $channels = DB::table( 'channels' )->select( 'name' )->get()->pluck( 'name' )->all();
        $threads->map( function ( $thread ) {
            $thread->channel_id = 1;
            $thread->save();
        } );

        // $threads->map( function ( $thread ) use ( $channels ) {
        //     $tags = $thread->tags;
        //     $tags = explode( ',', $tags );

        //     foreach ( $tags as $tag ) {
        //         if ( in_array( ucfirst( $tag ), $channels ) && $tag != '' ) {
        //             $channel = DB::table( 'channels' )->where( 'name', ucfirst( $tag ) )->first();
        //             $thread->channel_id = $channel->id;
        //             $thread->save();
        //             break;

        //         } else {
        //             $thread->channel_id = 1;
        //             $thread->save();

        //         }

        //     }
        // } );

        return 'channel Update complete';
    }

    public function addChannelToTags() {
        $channels = DB::table( 'channels' )->select( 'name' )->get()->pluck( 'name' )->all();

        foreach ( $channels as $channel ) {
            $tag = DB::table( 'tags' )->where( 'name', strtolower( $channel ) )->first();

            if ( !$tag ) {
                DB::table( 'tags' )->insert( ['name' => strtolower( $channel )] );
            } else {
                dump( 'not found ' );
            }
        }
    }

    public function attachTags() {
        $threads = Thread::where( 'tags', '!=', '' )->where( 'id', '>', 125000 )->where( 'id', '<', 130000 )->get();
        $threads->map( function ( $thread ) {
            $tags = $thread->tags;
            $tags = rtrim( $tags, ',' );
            $tags = explode( ',', $tags );

            foreach ( $tags as $tag ) {
                if ( $tag != '' ) {
                    $findTag = DB::table( 'tags' )->where( 'name', strtolower( $tag ) )->first();
                    if ( $findTag ) {
                        DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $findTag->id] );
                    } else {
                        //Error Here
                        $newTag = DB::table( 'tags' )->insertGetId( ['name' => strtolower( $tag )] );
                        // dd( $newTag );
                        DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $newTag] );
                    }
                }
            }
        } );

    }

    /**
     * Attach channel to tags
     */

    public function attachChannelTags() {
        // $threads = Thread::where( 'channel_id', '!=', '' )->where( 'id', '<', 5000 )->get();
        $threads = Thread::where( 'channel_id', '!=', '' )->where( 'id', '>', 119999 )->where( 'id', '<', 130000 )->get();
        // dd( $threads );

        $threads->map( function ( $thread ) {
            // DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $findTag->id] );
            $channel = DB::table( 'channels' )->where( 'id', $thread->channel_id )->first();
            $tag = DB::table( 'tags' )->where( 'name', strtolower( $channel->name ) )->first();
            if ( $tag ) {
                DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $tag->id] );
            }
        } );

    }

    /**
     * Remove fuplicatye title
     */

    public function removeDuplcateTitle() {
        //hello

        // $splitString = preg_split( "/('|:|-)/", $string );

        // return trim( array_shift( $splitString ) );

        $threads = Thread::where( 'id', '<', 5 )->get();
        // $threads = Thread::where( 'id', '>', 119999 )->where( 'id', '<', 130000 )->get();

        $threads->map( function ( $thread ) {
            // DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $findTag->id] );

            $splitString = preg_split( "/('|:|-)/", $thread->title );

            $newTitle = trim( array_shift( $splitString ) );
            $thread->title = $newTitle;
            $thread->save();

        } );

    }

    /**
     * Show wpl
     */

    public function scrape() {
        $threads = Thread::where( 'web_photo_link', '!=', '' )->limit( 10 )->get();
        // $threads = Thread::latest()->take( 1000 )->get();
        // dd( $threads );

        $threads->map( function ( $thread ) {
            if ( $thread->web_photo_link != '' ) {
                if ( filter_var( $thread->web_photo_link, FILTER_VALIDATE_URL ) ) {
                    $pos = strpos( $thread->web_photo_link, 'amazon.com' );

                    if ( $pos != FALSE ) {
                        $this->processAmazon( $thread );
                    } else {
                        $this->processWebPhotoLink( $thread );
                    }
                } else {
                    // save flag in error column
                    $thread->error = true;
                }
            } else {
                if ( $thread->wikipedia_mainpage_link != '' ) {
                    // If that exists,
                    // it will be an link to wikipedia page. Scrape to get the first image = image for the thread. Scrape description & license type. Image description = Wiki description + license type. Eg: "Mike Myers in 2000 (CC By-SA 2.0)"
                    $this->processWikipediaURL( $thread, $thread->wikipedia_mainpage_link );
                }
            }
        } );
    }

    function getFileExtensionFromURl( $url ) {
        $file = new \finfo( FILEINFO_MIME );
        $type = strstr( $file->buffer( file_get_contents( $url ) ), ';', true ); //Returns something similar to  image/jpg

        $extension = explode( '/', $type )[1];

        return $extension;
    }

    /**
     * Process web photo link
     */

    public function processWebPhotoLink( $thread ) {
        $description = $thread->photo_desc == '' ? '' : $thread->photo_desc;
        $pos = strpos( $thread->web_photo_link, 'wikimedia.org' );
        if ( $pos != false ) {
            //save web_photo_link to thread avatar
            $data = [
                'wiki_image_path' => $thread->web_photo_link,
                'description'     => $description,
                'image_saved'     => true,
            ];
            $this->saveInfo( $thread, $data );

        } else {
            //download image & save to database
            $extension = $this->getFileExtensionFromURl( $thread->web_photo_link );
            $fileName = md5( time() . uniqid() );
            $fullFileName = $fileName . '.' . $extension;
            $image_path = 'download/otherurl/' . $fullFileName;
            $fullPath = 'public/' . $image_path;

            $this->file_download_curl( $fullPath, $thread->web_photo_link );
            $data = [
                'other_image_url'  => $thread->web_photo_link,
                'other_image_path' => $fullPath,
                'description'      => $description,
                'image_saved'      => true,
            ];
            $this->saveInfo( $thread, $data );
        }
    }

    public function processAmazon( $thread ) {
        if ( $thread->amazon_product_link != '' ) {
            // $amazon_url = "http://www.amazon.com/gp/search?ie=UTF8&keywords=" . $thread->amazon_product_link;

            // $description = $thread->amazon_product_link . " " . '<a href="' . $amazon_url . '">Buy it here</a>' . "&tag=anecdotagecom-20";

            if ( filter_var( $thread->amazon_product_link, FILTER_VALIDATE_URL ) ) {
                //scrape amazon url
                $this->scrapeAmazon( $thread, $thread->amazon_product_link );
            } else {
                $description = $thread->amazon_product_link . " " . "Buy it here" . "&tag=anecdotagecom-20";
                $extension = $this->getFileExtensionFromURl( $thread->web_photo_link );
                $fileName = md5( time() . uniqid() );
                $fullFileName = $fileName . '.' . $extension;
                $image_path = 'download/amazon/' . $fullFileName;
                $fullPath = 'public/' . $image_path;

                $this->file_download_curl( $fullPath, $thread->web_photo_link );

                $data = [
                    'amazon_image_path'   => $fullPath,
                    // 'amazon_product_link' => $amazon_url,
                    'amazon_product_link' => $thread->amazon_product_link,
                    'description'         => $description,
                    'image_saved'         => true,
                    'error'               => true,
                ];
                $this->saveInfo( $thread, $data );
            }
        }

    }

    public function scrapeAmazon( $thread, $amazon_product_link ) {
        return $thread;

        $amazonUrl = "https://www.amazon.com/Friends-Television-Soundtrack/dp/B000002N1S";

        $client = new Client();
        $crawler = $client->request( 'GET', $amazonUrl );

        // $html =  $crawler->filter('table.infobox a.image img')->first();
        $anchor = $crawler->filter( 'table.infobox a.image' )->first();

        $href = $anchor->extract( ['href'] )[0];

    }

    public function file_download_curl( $fullPath, $full_image_link ) {
        $parts = explode( '/', $fullPath );
        array_pop( $parts );
        $dir = implode( '/', $parts );

        if ( !is_dir( $dir ) ) {
            mkdir( $dir, 0777, true );
        }

        $fp = fopen( $fullPath, 'wb' );
        $ch = curl_init( $full_image_link );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_FILE, $fp );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_exec( $ch );
        $error = curl_error( $ch );
        curl_close( $ch );
        fclose( $fp );
    }

    public function saveInfo( $thread, $data ) {
        $thread = Thread::where( 'id', $thread->id )->first();

        $thread->update( $data );
    }

    /**
     * Process wikipedia url
     */

    public function processWikipediaURL( $thread, $url ) {
        $client = new Client();
        $crawler = $client->request( 'GET', $url );

        // $html =  $crawler->filter('table.infobox a.image img')->first();
        $anchor = $crawler->filter( 'table.infobox a.image' )->first();

        if ( count( $anchor ) > 0 ) {
            $href = $anchor->extract( ['href'] )[0];
            $image_page_url = 'https://en.wikipedia.org' . $href;
            $image_page = $client->request( 'GET', $image_page_url );

            $full_image_link = $image_page->filter( '.fullImageLink a' )->first()->extract( ['href'] )[0];
            $full_image_link = str_replace( '//upload', 'upload', $full_image_link );
            $full_image_link = 'https://' . $full_image_link;

            $description = $image_page->filter( 'td.description' );
            $description = ( $description->count() > 0 ) ? $description->first()->text() : "";

            $license = $image_page->filter( 'table.licensetpl span.licensetpl_short' );
            $license = ( $license->count() > 0 ) ? $license->first()->text() : "";

            $description = str_replace( 'English: ', '', $description );
            $description = $description . "(" . $license . ")";

            if ( $full_image_link != '' ) {
                $data = [
                    'wiki_info_page_url'  => $url,
                    'wiki_image_page_url' => $image_page_url,
                    'wiki_image_url'      => $full_image_link,
                    'wiki_image_path'     => $full_image_link,
                    'description'         => $description,
                    'image_saved'         => false,

                ];
                $this->saveInfo( $thread, $data );
            }

        }
    }

}