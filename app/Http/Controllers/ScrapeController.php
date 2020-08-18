<?php

namespace App\Http\Controllers;

use App\Jobs\ScrappingJob;
use App\Thread;
use Goutte\Client;

class ScrapeController extends Controller {
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
     * Srape info
     */

    public function scrape() {
        // $threads = Thread::where( 'id', '<', 5001 )->get();
        $threads = Thread::where( 'id', '<', 150001 )->where( 'id', '>', '120000' )->get();
        // $threads = Thread::latest()->take( 1000 )->get();
        // dd( $threads );

        $threads->map( function ( $thread ) {
            ScrappingJob::dispatch( $thread );
            // WikiImageProcess::dispatch( request( 'wiki_info_page_url' ), $thread, false );

            // if ( $thread->web_photo_link != '' ) {
            //     if ( filter_var( $thread->web_photo_link, FILTER_VALIDATE_URL ) ) {
            //         $pos = strpos( $thread->web_photo_link, 'amazon.com' );

            //         if ( $pos != FALSE ) {
            //             $this->processAmazon( $thread );
            //         } else {
            //             $this->processWebPhotoLink( $thread );
            //         }
            //     } else {
            //         // save flag in error column
            //         $thread->error = true;
            //     }
            // } else {
            //     if ( $thread->wikipedia_mainpage_link != '' ) {
            //         $this->processWikipediaURL( $thread, $thread->wikipedia_mainpage_link );
            //     }
            // }
        } );
    }

    /**
     * Process web photo link
     */

    public function processWebPhotoLink( $thread ) {
        $description = $thread->photo_desc == '' ? '' : $thread->photo_desc;
        $pos = strpos( $thread->web_photo_link, 'wikimedia.org' );
        if ( $pos != false ) {
            $data = [
                'wiki_image_path' => $thread->web_photo_link,
                'description'     => $description,
                'image_saved'     => false,
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

    public function scrapeAmazon( $thread, $amazonUrl ) {
        // $amazonUrl = "https://www.amazon.com/Friends-Television-Soundtrack/dp/B000002N1S";

        $pos = strpos( $amazonUrl, 'images-amazon.com' );
        if ( $pos != false ) {
            $data = [
                'error' => true,
            ];
            $this->saveInfo( $thread, $data );
        } else {
            $client = new Client();
            $crawler = $client->request( 'GET', $amazonUrl );
            $title = $crawler->filter( 'span#productTitle' )->first()->text();
            $title = $title . ' <a href="' . $amazonUrl . "&tag=anecdotagecom-20" . '">BUY IT HERE</a>';

            $imageWrapper = $crawler->filter( 'div#imgTagWrapperId img' );
            $imageCanvas = $crawler->filter( 'div#img-canvas img' );
            if ( $imageWrapper->count() > 0 ) {
                $image = $imageWrapper->first()->extract( ['src'] )[0];
                $image_name = md5( time() . uniqid() ) . '.jpg';
                $image_name = 'public/download/amazon/' . $image_name;

                $this->base64ToImage( $image, $image_name );

                $data = [
                    'amazon_image_path'   => $image_name,
                    'amazon_product_link' => $thread->amazon_product_link,
                    'description'         => $title,
                    'image_saved'         => true,
                ];
                $this->saveInfo( $thread, $data );
            } else if ( $imageCanvas->count() > 0 ) {
                $image = $imageCanvas->first()->extract( ['src'] )[0];
                $image_name = md5( time() . uniqid() ) . '.jpg';
                $image_name = 'public/download/amazon/' . $image_name;

                $this->base64ToImage( $image, $image_name );
                $data = [
                    'amazon_image_path'   => $image_name,
                    'amazon_product_link' => $thread->amazon_product_link,
                    'description'         => $title,
                    'image_saved'         => true,
                ];
                $this->saveInfo( $thread, $data );
            }
        }

    }

    function base64ToImage( $base64_string, $output_file ) {
        $parts = explode( '/', $output_file );
        array_pop( $parts );
        $dir = implode( '/', $parts );
        // dd( $output_file );
        if ( !is_dir( $dir ) ) {
            mkdir( $dir, 0777, true );
        }

        $file = fopen( $output_file, "wb" );

        $data = explode( ',', $base64_string );

        fwrite( $file, base64_decode( $data[1] ) );
        fclose( $file );

        return $output_file;
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
        // curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
        curl_exec( $ch );
        $error = curl_error( $ch );
        curl_close( $ch );
        fclose( $fp );
    }

    public function saveInfo( $thread, $data ) {
        $thread = Thread::where( 'id', $thread->id )->first();
        $thread->update( $data );
    }

    function getFileExtensionFromURl( $url ) {
        $file = new \finfo( FILEINFO_MIME );
        $type = strstr( $file->buffer( file_get_contents( $url ) ), ';', true ); //Returns something similar to  image/jpg

        $extension = explode( '/', $type )[1];

        return $extension;
    }

    public function scrapeImage() {
        $url = 'https://cdn.newsapi.com.au/image/v1/ee4f5f7d2d1249e3b3c5c40ddff7a289';

        // $extension = $this->getFileExtensionFromURl( $url );
        $extension = 'jpg';
        $fileName = md5( time() . uniqid() );
        $fullFileName = $fileName . '.' . $extension;
        $image_path = 'public/download/otherurl/' . $fullFileName;

        // $this->file_download_curl( $fullPath, $url );
        dump( $image_path );
    }

    public function checkHTML() {
        $threads = Thread::where( 'amazon_image_path', 'LIKE', '%ff5fbfdc7c258a3db251ffd4ff9541cd%' )->get();

        return $threads;
    }

    public function checkImageURl() {
        $url = 'https://en.wikipedia.org/wiki/Kate_Smith#/media/File:Kate_Smith_Billboard_4.jpg';

        return $this->getFileExtensionFromURl( $url );
    }

}
