<?php

namespace App\Jobs;

use App\NewTag;
use App\Tags;
use DB;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use phpDocumentor\Reflection\DocBlock\Tag;

class NewTagScraping implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
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
        $this->scrapeWithKeyword();
    }

    public function scrapeWithKeyword() {
        $keyword = $this->tag->name;

        // dump( $keyword );

        $keyword = ucwords( $keyword );
        $keyword = str_replace( ' ', '_', $keyword );
        $newUrl = 'https://en.wikipedia.org/wiki' . '/' . $keyword;

        $client = new Client();
        $crawler = $client->request( 'GET', $newUrl );

        $infobox = $crawler->filter( 'table.infobox a.image' )->first();

        if ( count( $infobox ) ) {
            $href = $infobox->extract( ['href'] )[0];
            $image_page_url = 'https://en.wikipedia.org' . $href;
        } else {
            $thumbinner = $crawler->filter( 'div.thumbinner a.image' )->first();

            if ( count( $thumbinner ) > 0 ) {
                $href = $thumbinner->extract( ['href'] )[0];
                $image_page_url = 'https://en.wikipedia.org' . $href;
            }

        }

        $this->scrpeImagePageUrl( $image_page_url );
    }

    /**
     * @param $image_page_url
     */
    public function scrpeImagePageUrl( $image_page_url ) {
        $client = new Client();

        $authorText = '';
        $htmlLicense = '';
        $descriptionText = '';
        // $shopText = "http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={$this->tag->name}&linkCode=ur2&tag=anecdotagecom-20";
        $shopText = '<a class="btn btn-xs btn-primary" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords='.$this->tag->name.'&linkCode=ur2&tag=anecdotage01-20">Shop for '.$this->tag->name.'</a>';

        $image_page = $client->request( 'GET', $image_page_url );

        if ( $image_page->filter( 'span.mw-filepage-other-resolutions' )->count() > 0 ) {
            $full_image_link = $image_page->filter( 'span.mw-filepage-other-resolutions a' )->first()->extract( ['href'] )[0];
        } else

        if ( $image_page->filter( '.fullImageLink a' )->count() > 0 ) {
            $full_image_link = $image_page->filter( '.fullImageLink a' )->first()->extract( ['href'] )[0];
        }

        $full_image_link = str_replace( '//upload', 'upload', $full_image_link );
        $full_image_link = 'https://' . $full_image_link;

        if ( isset( $full_image_link ) ) {
                $description = $image_page->filter( 'div.description' );

                if ( $description->count() > 0 ) {
                    $description = $description->first()->text();
                    $descriptionText = str_replace( 'English: ', '', $description );
                }

                $license = $image_page->filter( 'table.licensetpl span.licensetpl_short' );

                if ( $license->count() > 0 ) {
                    $saLicenseType = [
                        'CC BY-SA 1.0',
                        'CC BY-SA 1.5',
                        'CC BY-SA 2.5',
                        'CC BY-SA 3.0',
                        'CC BY-SA 4.0',
                    ];
                    $nonSaLicenseType = [
                        'CC BY 1.0',
                        'CC BY 1.5',
                        'CC BY 2.0 ',
                        'CC BY 2.5 ',
                        'CC BY 3.0',
                        'CC BY 4.0',
                    ];

                    $licenseText = $license->first()->text();
                    if( $licenseText == 'Public domain'){
                        $htmlLicense = 'Public domain';
                    }
                    else if ( in_array( $licenseText, $saLicenseType ) ) {
                       if( \preg_match('&(\d)\.?\d?&',$licenseText, $matches)){

                           $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/'.$matches[0].'">'.$licenseText.'</a>';
                       }
                    }else if ( in_array( $licenseText, $nonSaLicenseType ) ) {
                        if(\preg_match('&(\d)\.?\d?&',$licenseText, $matches)){

                            $htmlLicense = '<a href="https://creativecommons.org/licenses/by/'.$matches[0].'">'.$licenseText.'</a>';
                        }
                    }
                    
                    if($htmlLicense != ''){
                        \dump($htmlLicense);
                    }else{
                        \dump('other license');
                    }

                     // https://creativecommons.org/licenses/by/1.0/
                        // https://creativecommons.org/licenses/by/2.0/
                        // https://creativecommons.org/licenses/by/2.5/
                        // https://creativecommons.org/licenses/by/3.0/
                        // https://creativecommons.org/licenses/by/4.0/
                        // https://creativecommons.org/licenses/by-sa/1.0/
                        // https://creativecommons.org/licenses/by-sa/2.0/
                        // https://creativecommons.org/licenses/by-sa/2.5/
                        // https://creativecommons.org/licenses/by-sa/3.0/
                        // https://creativecommons.org/licenses/by-sa/4.0/
                        // 'Public domain', -- No link needed
                        // 'CC BY-SA 1.5' -- Not EXISTS, USE: 1.0

                }

                $author = $image_page->filter( 'td#fileinfotpl_aut' );

                if ( $author->count() > 0 ) {
                    $newAuthor = $image_page->filter( 'td#fileinfotpl_aut' )->nextAll();
                    $newAuthor = $newAuthor->filter( 'a' );

                    if ( $newAuthor->count() > 0 ) {
                        $authorText = $newAuthor->first()->text();
                    }
                }

                $fullDescriptionText = sprintf( '%s %s %s %s', $descriptionText, $authorText, $htmlLicense, $shopText );
                $data = [
                    'photo'       => $full_image_link,
                    'description' => $fullDescriptionText,
                ];
                $this->saveInfo( $data );
             

        }

    }

    /**
     * @param $data
     */
    public function saveInfo( $data ) {
        $this->tag->update( $data );
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
