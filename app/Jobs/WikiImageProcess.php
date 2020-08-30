<?php

namespace App\Jobs;

use App\Thread;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WikiImageProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $wikiUrl;
    protected $thread;
    protected $isDelete;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wikiUrl, $thread, $isDelete)
    {
        $this->wikiUrl = $wikiUrl;
        $this->thread = $thread;
        $this->isDelete = $isDelete;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->wikiUrl !=null){
            $client = new Client();
            $crawler = $client->request('GET', $this->wikiUrl);
        
            // $html =  $crawler->filter('table.infobox a.image img')->first();
            $anchor =  $crawler->filter('table.infobox a.image')->first();
            
            Log::info('calling me');

            if(count($anchor)>0){
    
                $href = $anchor->extract(['href'])[0];
                $image_page_url = 'https://en.wikipedia.org' .$href;    
                $image_page = $client->request('GET', $image_page_url);

                $full_image_link =  $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
                $full_image_link = str_replace('//upload','upload', $full_image_link);
                $full_image_link = 'https://'.$full_image_link;
    
                $description = $image_page->filter('td.description');
                $description = ($description->count()>0) ? $description->first()->text() : "";
                
                $license = $image_page->filter('table.licensetpl span.licensetpl_short');
                $license = ($license->count()>0) ? $license->first()->text() : "";
    
                $description = str_replace('English: ','', $description);
                $description = $description."(".$license.")";

                if($full_image_link != ''){
                    $fileExtension = explode('.', $full_image_link);
                    $fileExtension = array_pop($fileExtension);
                    $fileName = md5(time().uniqid());
                    $fullFileName = $fileName.'.'.$fileExtension;
                    $image_path = 'download/wikipedia/'.$fullFileName;
                    $fullPath = 'public/'.$image_path;

                    $this->file_download_curl($fullPath, $full_image_link );
                    $this->saveInfo($image_page_url, $full_image_link, $description, $image_path);
                    if($this->isDelete){
                        $this->deleteImage($this->thread->wiki_image_path);
                    }
                }
    
            }
        }
    }

    public function file_download_curl($fullPath, $full_image_link){
        $parts = explode( '/', $fullPath );
        array_pop( $parts );
        $dir = implode( '/', $parts );
       
        if( !is_dir( $dir ) ){
            mkdir( $dir, 0777, true );
        }

        $fp = fopen($fullPath, 'wb');
        $ch = curl_init($full_image_link); 
        curl_setopt($ch, CURLOPT_FILE, $fp); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch); 
        fclose($fp); 
    }

    public function saveInfo($image_page_url, $full_image_link, $description, $fullPath){
        $thread = Thread::where('id', $this->thread->id)->first();

        $thread->wiki_info_page_url = $this->wikiUrl;
        $thread->wiki_image_page_url = $image_page_url;
        $thread->wiki_image_url = $full_image_link;
        $thread->wiki_image_path = $fullPath;
        // $thread->wiki_info_page_url = $wikiUrl;
        $thread->description = $description;
        $thread->image_saved = true;

        $thread->save();
    }

    public function deleteImage($url){
        echo public_path().'/'.'download/wikipedia/c7147575d2710acf83ef9be4b04d4309.jpg';
        echo "\n";
        echo public_path().'/'.$url;

        if(\File::exists(public_path().'/'.$url)){

            \File::delete(public_path().'/'.$url);
        
          }else{
        
            echo ('File does not exists.');
        
          }
    }
}
