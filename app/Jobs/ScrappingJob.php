<?php

namespace App\Jobs;

use App\Thread;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrappingJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    protected $thread;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $thread ) {
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        if ( $this->thread->web_photo_link != '' ) {

            if ( filter_var( $this->thread->web_photo_link, FILTER_VALIDATE_URL ) ) {
                $pos = strpos( $this->thread->web_photo_link, 'amazon.com' );

                if ( $pos != FALSE ) {
                    $this->processAmazon();
                } else {
                    $this->processWebPhotoLink();
                }

            } else {
                $data = [
                    'error' => true,
                ];
                $this->saveInfo( $data );
            }

        } else {

            if ( $this->thread->wikipedia_mainpage_link != '' ) {
                $this->processWikipediaURL();
            }

        }

    }

    /**
     * Process web photo link
     */

    public function processWebPhotoLink() {
        $description = $this->thread->photo_desc == '' ? '' : $this->thread->photo_desc;
        $pos = strpos( $this->thread->web_photo_link, 'wikimedia.org' );

        if ( $pos != false ) {
            $data = [
                'wiki_image_path' => $this->thread->web_photo_link,
                'description'     => $description,
                'image_saved'     => false,
            ];
            $this->saveInfo( $data );

        } else {
            //download image & save to database
            $extension = $this->getFileExtensionFromURl( $this->thread->web_photo_link );
            $fileName = md5( time() . uniqid() );
            $fullFileName = $fileName . '.' . $extension;
            $image_path = 'download/otherurl/' . $fullFileName;
            $fullPath = 'public/' . $image_path;

            $this->file_download_curl( $fullPath, $this->thread->web_photo_link );
            $data = [
                'other_image_url'  => $this->thread->web_photo_link,
                'other_image_path' => $fullPath,
                'description'      => $description,
                'image_saved'      => true,
            ];
            $this->saveInfo( $data );
        }

    }

    /**
     * Process wikipedia url
     */

    public function processWikipediaURL() {
        $client = new Client();
        $crawler = $client->request( 'GET', $this->thread->wikipedia_mainpage_link );

        $anchor = $crawler->filter( 'table.infobox a.image' )->first();

        if ( count( $anchor ) > 0 ) {
            $href = $anchor->extract( ['href'] )[0];
            $image_page_url = 'https://en.wikipedia.org' . $href;
            $image_page = $client->request( 'GET', $image_page_url );

            $full_image_link = $image_page->filter( '.fullImageLink a' )->first()->extract( ['href'] )[0];
            $full_image_link = str_replace( '//upload', 'upload', $full_image_link );
            $full_image_link = 'https://' . $full_image_link;

            $description = $image_page->filter( 'td.description' );
            $description = ( $description->count() > 0 ) ? $description->first()->text() : '';

            $license = $image_page->filter( 'table.licensetpl span.licensetpl_short' );
            $license = ( $license->count() > 0 ) ? $license->first()->text() : '';

            $description = str_replace( 'English: ', '', $description );
            $description = $description . '(' . $license . ')';

            if ( $full_image_link != '' ) {
                $data = [
                    'wiki_info_page_url'  => $this->thread->wikipedia_mainpage_link,
                    'wiki_image_page_url' => $image_page_url,
                    'wiki_image_url'      => $full_image_link,
                    'wiki_image_path'     => $full_image_link,
                    'description'         => $description,
                    'image_saved'         => false,

                ];
                $this->saveInfo( $data );
            }

        }

    }

    public function processAmazon() {

        if ( $this->thread->amazon_product_link != '' ) {

// $amazon_url = "http://www.amazon.com/gp/search?ie=UTF8&keywords=" . $thread->amazon_product_link;

// $description = $thread->amazon_product_link . " " . '<a href="' . $amazon_url . '">Buy it here</a>' . "&tag=anecdotagecom-20";

            if ( filter_var( $this->thread->amazon_product_link, FILTER_VALIDATE_URL ) ) {
                //scrape amazon url
                $this->scrapeAmazon();
            } else {
                $description = $this->thread->amazon_product_link . ' ' . 'Buy it here' . '&tag=anecdotagecom-20';
                $extension = $this->getFileExtensionFromURl( $this->thread->web_photo_link );
                $fileName = md5( time() . uniqid() );
                $fullFileName = $fileName . '.' . $extension;
                $image_path = 'download/amazon/' . $fullFileName;
                $fullPath = 'public/' . $image_path;

                $this->file_download_curl( $fullPath, $this->thread->web_photo_link );

                $data = [
                    'amazon_image_path'   => $fullPath,
                    'amazon_product_link' => $this->thread->amazon_product_link,
                    'description'         => $description,
                    'image_saved'         => true,
                    'error'               => true,
                ];
                $this->saveInfo( $data );
            }

        }

    }

    public function scrapeAmazon() {
        $amazonUrl = $this->thread->amazon_product_link;
        $pos = strpos( $amazonUrl, 'images-amazon.com' );
        $posMedia = strpos( $amazonUrl, 'media-amazon.com' );

        if ( $pos != false ) {
            $data = [
                'error' => true,
            ];
            $this->saveInfo( $data );
        }

        if ( $pos != false ) {
            $data = [
                'error' => true,
            ];
            $this->saveInfo( $data );
        } else {
            //Errorv Here
            $client = new Client();
            $crawler = $client->request( 'GET', $amazonUrl );
            $title = $crawler->filter( 'span#productTitle' );
            $detailTitle = $crawler->filter( 'span#productTitle' );

            if ( $title->count() > 0 ) {
                $title = $title->first()->text();
            } else
            if ( $detailTitle->count() > 0 ) {
                $title = $detailTitle->first()->text();
            } else {
                $title = '';
            }

            $title = $title . ' <a href="' . $amazonUrl . '&tag=anecdotagecom-20' . '">BUY IT HERE</a>';

            $imageWrapper = $crawler->filter( 'div#imgTagWrapperId img' );
            $imageCanvas = $crawler->filter( 'div#img-canvas img' );

            if ( $imageWrapper->count() > 0 ) {
                $image = $imageWrapper->first()->extract( ['src'] )[0];
                $image_name = md5( time() . uniqid() ) . '.jpg';
                $image_name = 'public/download/amazon/' . $image_name;

                $this->base64ToImage( $image, $image_name );

                $data = [
                    'amazon_image_path'   => $image_name,
                    'amazon_product_link' => $this->thread->amazon_product_link,
                    'description'         => $title,
                    'image_saved'         => true,
                ];
                $this->saveInfo( $data );
            } else
            if ( $imageCanvas->count() > 0 ) {
                $image = $imageCanvas->first()->extract( ['src'] )[0];
                $image_name = md5( time() . uniqid() ) . '.jpg';
                $image_name = 'public/download/amazon/' . $image_name;

                $this->base64ToImage( $image, $image_name );
                $data = [
                    'amazon_image_path'   => $image_name,
                    'amazon_product_link' => $this->thread->amazon_product_link,
                    'description'         => $title,
                    'image_saved'         => true,
                ];
                $this->saveInfo( $data );
            }

        }

    }

    /**
     * @param $base64_string
     * @param $output_file
     * @return mixed
     */
    public function base64ToImage( $base64_string, $output_file ) {
        $parts = explode( '/', $output_file );
        array_pop( $parts );
        $dir = implode( '/', $parts );

        if ( !is_dir( $dir ) ) {
            mkdir( $dir, 0777, true );
        }

        $file = fopen( $output_file, 'wb' );

        $data = explode( ',', $base64_string );

        fwrite( $file, base64_decode( $data[1] ) );
        fclose( $file );

        return $output_file;
    }

    /**
     * @param $fullPath
     * @param $full_image_link
     */
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

        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
        curl_exec( $ch );
        $error = curl_error( $ch );
        curl_close( $ch );
        fclose( $fp );
    }

    /**
     * @param $data
     */
    public function saveInfo( $data ) {
        $thread = Thread::where( 'id', $this->thread->id )->first();
        $thread->update( $data );
    }

    /**
     * @param $url
     * @return mixed
     */
    function getFileExtensionFromURl( $url ) {
        $file = new \finfo( FILEINFO_MIME );
        $type = strstr( $file->buffer( file_get_contents( $url ) ), ';', true ); //Returns something similar to  image/jpg

        $extension = explode( '/', $type )[1];

        return $extension;
    }

}
