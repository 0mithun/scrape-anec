<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Goutte\Client;

class ScrapeMissingDescriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     
        $client = new Client();
        $crawler = $client->request( 'GET', $this->thread->wiki_info_page_url );
       dump($this->thread->wiki_info_page_url);

        $thumbcaption = $crawler->filter( 'div.thumbcaption' )->first();
        if($thumbcaption->count()>0){
            $description = $thumbcaption->first()->text();
            $licenseText = '<a href="https://creativecommons.org/licenses/by/2.0/">CC BY SA 2.0</a>';
            $fullDescriptionText = $description.' '.$licenseText;

           
        }
        $data = [
            'wiki_image_description' => $fullDescriptionText,
        ];

        
        // $thumbinner = $crawler->filter( 'div.thumbinner a.image' )->first();

        // $descriptionText = str_replace( 'English: ', '', $description );

        
        $this->saveInfo( $data );

    }


    /**
     * @param $data
     */
    public function saveInfo( $data ) {
        $this->thread->update( $data );
    }

}
