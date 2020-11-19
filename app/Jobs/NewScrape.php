<?php

namespace App\Jobs;

use App\Thread;
use DB;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewScrape implements ShouldQueue {
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
    public function __construct( Thread $thread ) {
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $this->processWikipediaURL();
    }

    /**
     * Process wikipedia url
     */

    public function processWikipediaURL() {
        $client = new Client();

// $newUrl = 'https://en.wikipedia.org/wiki' . '/' . $keyword;

// $crawler = $client->request( 'GET', $this->thread->wiki_info_page_url );

// $anchor = $crawler->filter( 'table.infobox a.image' )->first();

// $infobox = $crawler->filter( 'table.infobox a.image' )->first();

// if ( count( $infobox ) ) {

//     $href = $infobox->extract( ['href'] )[0];

//     $image_page_url = 'https://en.wikipedia.org' . $href;

// } else {

//     $thumbinner = $crawler->filter( 'div.thumbinner a.image' )->first();

//     if ( count( $thumbinner ) > 0 ) {

//         $href = $thumbinner->extract( ['href'] )[0];

//         $image_page_url = 'https://en.wikipedia.org' . $href;

//     }

// }

// dump( $image_page_url );

// dump( $this->thread->wiki_image_page_url );
        // info( $this->thread->id );
        DB::table( 'image_page_not_found' )->insert( ['thread_id' => $this->thread->id, 'image_path' => $this->thread->wiki_image_path] );
        $image_page = $client->request( 'GET', $this->thread->wiki_image_page_url );

// $main_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-en_wikiquote_org' )->count();

// $main_page = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-en_wikipedia_org' )->count();

        $wiki_info_page_url = '';

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-en_wikiquote_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-en_wikiquote_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-en_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-en_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-es_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-es_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ca_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ca_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-it_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-it_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-lt_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-lt_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ga_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ga_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-an_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-an_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ay_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ay_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-vls_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-vls_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ru_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ru_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-bs_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-bs_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-sr_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-sr_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-io_wikipedia_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-io_wikipedia_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-es_wikiquote_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-es_wikiquote_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ca_wikiquote_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-ca_wikiquote_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-lt_wikiquote_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-lt_wikiquote_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-it_wikiquote_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-it_wikiquote_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-fr_wikiquote_org' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'div#mw-imagepage-section-globalusage li.mw-gu-onwiki-fr_wikiquote_org a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( 'li.mw-imagepage-linkstoimage-ns0' )->count() > 0 ) {
            $wiki_info_page_url = $image_page->filter( 'li.mw-imagepage-linkstoimage-ns0 a' )->first()->extract( ['href'] )[0];
        }

        if ( $wiki_info_page_url != '' && (  ( strpos( $wiki_info_page_url, 'wikipedia.org' ) == false ) && ( strpos( $wiki_info_page_url, 'wikiquote.org' ) == false ) ) ) {
            $wiki_info_page_url = 'wikipedia.org' .
                $wiki_info_page_url;
        }

        if ( $image_page->filter( 'span.mw-filepage-other-resolutions' )->count() > 0 ) {
            $full_image_link = $image_page->filter( 'span.mw-filepage-other-resolutions a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( '.fullImageLink a' )->count() > 0 ) {
            $full_image_link = $image_page->filter( '.fullImageLink a' )->first()->extract( ['href'] )[0];
        }

        dump( $this->thread->wiki_image_page_url );

// dump( $full_image_link );

// $full_image_link = $image_page->filter( '.fullImageLink a' )->first()->extract( ['href'] )[0];

// if ( $image_page->filter( '.fullImageLink a' )->count() > 0 ) {

//     $full_image_link = $image_page->filter( '.fullImageLink a' )->first()->extract( ['href'] )[0];

//     $full_image_link = str_replace( '//upload', 'upload', $full_image_link );

//     $full_image_link = 'https://' . $full_image_link;

// }

        if ( isset( $full_image_link ) ) {

            $full_image_link = str_replace( '//upload', 'upload', $full_image_link );

            $full_image_link = 'https://' . $full_image_link;

            $description = $image_page->filter( 'td.description' );

            $description = ( $description->count() > 0 ) ? $description->first()->text() : '';

            $license = $image_page->filter( 'table.licensetpl span.licensetpl_short' );

            $license = ( $license->count() > 0 ) ? $license->first()->text() : '';

            $description = str_replace( 'English: ', '', $description );

            $description = $description . '(' . $license . ')';

            if ( $full_image_link != '' ) {
                $pixel_color = $this->getImageColorAttribute( $full_image_link );

                $data = [

                    'wiki_info_page_url'          => $wiki_info_page_url,

                    // 'wiki_image_page_url' => $image_page_url,

                    'wiki_image_url'              => $full_image_link,

                    'wiki_image_path'             => $full_image_link,
                    'wiki_image_path_pixel_color' => $pixel_color,

                    'description'                 => $description,

                    'image_saved'                 => false,

                ];
                dump( $full_image_link );
                $this->saveInfo( $data );
            }

        } else {
            dump( 'Full Image Link Not Found', $this->thread->id );
        }

    }

    /**
     * @param $data
     */
    public function saveInfo( $data ) {

        $this->thread->update( $data );
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

    /**
     * Get rgb color value from image
     */

    public function getImageColorAttribute( $image_path ) {

        if ( $image_path != '' ) {
            $splitName = explode( '.', $image_path );
            $extension = strtolower( array_pop( $splitName ) );

            if ( $extension == 'jpg' ) {
                $im = imagecreatefromjpeg( $image_path );
            }

            if ( $extension == 'jpeg' ) {
                $im = imagecreatefromjpeg( $image_path );
            } else

            if ( $extension == 'png' ) {
                $im = imagecreatefrompng( $image_path );
            } else

            if ( $extension == 'gif' ) {
                $im = imagecreatefromgif( $image_path );
            }

            if ( isset( $im ) ) {

                $rgb = imagecolorat( $im, 0, 0 );
                $colors = imagecolorsforindex( $im, $rgb );
                array_pop( $colors );
                array_push( $colors, 1 );
                $rgbaString = join( ', ', $colors );

                return $rgbaString;
            }

        }

        return false;
    }

}
