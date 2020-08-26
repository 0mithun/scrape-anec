<?php

namespace App\Jobs;

use App\Tags;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TagImageProcessing implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tag;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Tags $tag ) {
        $this->tag = $tag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        if ( $this->tag->name != null ) {
            $client = new Client();

            $url = 'https://en.wikipedia.org/wiki/' . $this->tag->name;
            $crawler = $client->request( 'GET', $url );

            $meta = $crawler->filter( 'meta' );

            $meta->each( function ( $node ) {
                $property = $node->extract( ['property'] )[0];
                if ( $property != '' ) {
                    $full_image_link = $node->extract( ['content'] )[0];
                    // $fileExtension = explode( '.', $full_image_link );
                    // $fileExtension = array_pop( $fileExtension );
                    // $fileName = md5( now() . uniqid() );

                    // $fullFileName = $fileName . '.' . $fileExtension;
                    // $image_path = strtolower( 'download/tags/' . $fullFileName );
                    // $fullPath = 'public/' . $image_path;

                    // $this->deleteImage( $this->tag->photo );
                    // $this->file_download_curl( $fullPath, $full_image_link );
                    // $this->saveInfo( $image_path );

                    $this->saveInfo( $full_image_link );

                }

            } );

        }
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
        curl_setopt( $ch, CURLOPT_FILE, $fp );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_exec( $ch );
        $error = curl_error( $ch );
        curl_close( $ch );
        fclose( $fp );
    }

    public function saveInfo( $image_path ) {
        $tag = Tags::where( 'id', $this->tag->id )->first();
        $tag->photo = $image_path;
        $tag->save();
    }

    public function deleteImage( $url ) {
        if ( \File::exists( public_path() . '/' . $url ) ) {
            \File::delete( public_path() . '/' . $url );
            echo "file Deleted" . PHP_EOL;

        } else {
            echo ( 'File does not exists.' ) . PHP_EOL;

        }
    }
}
