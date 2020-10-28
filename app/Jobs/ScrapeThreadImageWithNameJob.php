<?php

namespace App\Jobs;

use App\Thread;

use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DB;

class ScrapeThreadImageWithNameJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     *
     * @var $thread
     */
   public $thread;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $nameLists = DB::table('namelist')->get()->pluck('name');

        $found = false;
        foreach($nameLists as $nameList){
            if(stripos($this->thread->body, $nameList)){              
               $found = true;
                break;
            }
        }

        if($found){
            $nameList;
              $this->scrapeWithKeyword($nameList);
        }        
    }


    public function scrapeWithKeyword($keyword) {

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

        if(isset($image_page_url)){
            
            $this->scrpeImagePageUrl( $image_page_url, $newUrl);
        }

    }


    /**
     * @param $image_page_url
     */
    public function scrpeImagePageUrl( $image_page_url, $newUrl ) {
        dump('Scrap Image Page Url');
        $client = new Client();

        $htmlLicense = '';
        $descriptionText = '';
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
                    $htmlLicense = '(Public domain)';
                }
                else if ( in_array( $licenseText, $saLicenseType ) ) {
                    if( \preg_match('&(\d)\.?\d?&',$licenseText, $matches)){

                        $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/'.$matches[0].'">('.$licenseText.')</a>';
                    }
                }else if ( in_array( $licenseText, $nonSaLicenseType ) ) {
                    if(\preg_match('&(\d)\.?\d?&',$licenseText, $matches)){

                        $htmlLicense = '<a href="https://creativecommons.org/licenses/by/'.$matches[0].'">('.$licenseText.')</a>';
                    }
                } 
                
               

               
            }

            if($this->thread->amazon_product_url != ''){
                $shopText = '<a class="btn btn-xs btn-primary" href="'.$this->thread->amazon_product_url.'&linkCode=ur2&tag=anecdotage01-20">Shop</a>';

                $fullDescriptionText = sprintf( '%s %s %s', $descriptionText,  $htmlLicense, $shopText  );
            }else{
                
                $fullDescriptionText = sprintf( '%s %s', $descriptionText,  $htmlLicense  );
            }

            $data = [
                'wiki_info_page_url' => $newUrl,
                'description' => $fullDescriptionText,
                'wiki_image_description' => $fullDescriptionText,
                'wiki_image_page_url' =>  $image_page_url,
                'wiki_image_url' =>  $full_image_link,
                'wiki_image_path' =>  $full_image_link,
            ];

            dump($data);
            // $this->saveInfo( $data );

            // $shopText = '<a class="btn btn-xs btn-primary" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords='.$this->tag->name.'&linkCode=ur2&tag=anecdotage01-20">Shop for '.$this->tag->name.'</a>';

        }

    }




    public function saveInfo(array $data){
        $this->thread->update($data);
    }
}
