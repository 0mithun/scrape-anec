<?php

namespace App\Jobs;

use Goutte\Client;
use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InsertAmazonProductUrlToThreadsTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $thread;
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
        // dump($this->article);
        // $thread = Thread::where('id', $this->article->article_id)->first();

        // if ($thread) {
        //     //Do logic
        //     $data = [
        //         'amazon_product_url'    =>  $this->article->amazon_product_link
        //     ];
        //     $thread->update($data);
        // }

        $this->processAmazon();
    }



    public function processAmazon()
    {

        if ($this->thread->amazon_product_url != '' || $this->thread->amazon_product_url != null) {
            if (filter_var($this->thread->amazon_product_url, FILTER_VALIDATE_URL)) {
                //scrape amazon url
                $this->scrapeAmazon();
            } else {

                // Else (amazon_product_link is some string like "James Bond")
                //     {
                //     description = amazon_product_link + " [Shop]"
                //     scrape image from: "https://en.wikipedia.org/wiki/".$amazon_product_link
                //     Eg: https://en.wikipedia.org/wiki/James%20Bond
                //     link image & description to:
                //     "http://www.amazon.com/gp/search?ie=UTF8&keywords=".$amazon_product_link.
                //     "&tag=anecdotagecom-20"
                //     }

                if ($this->thread->wiki_image_path != '' || $this->thread->wiki_image_path = NULL) {
                    $description = $this->thread->amazon_product_url .  '<a class="btn btn-xs btn-primary" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=' . $this->thread->amazon_product_url . '&linkCode=ur2&tag=anecdotage01-20">Shop for funny</a>';

                    $data = [
                        'description'   => $description,
                        'wiki_image_description'    => $description,
                    ];
                    $this->saveInfo($data);
                }
            }
        }
    }

    public function scrapeAmazon()
    {
        $amazonUrl = $this->thread->amazon_product_url;
        $pos = strpos($amazonUrl, 'images-amazon.com');
        $posMedia = strpos($amazonUrl, 'media-amazon.com');
        dump($this->thread->id);
        dump($amazonUrl);

        if ($pos != false) {
            // $data = [
            //     'error' => true,
            // ];
            // $this->saveInfo($data);
        } else if ($posMedia != false) {
            // $data = [
            //     'error' => true,
            // ];
            // $this->saveInfo($data);
        } else {
            //Errorv Here
            $client = new Client();
            $crawler = $client->request('GET', $amazonUrl);
            $title = $crawler->filter('span#productTitle');
            $detailTitle = $crawler->filter('span#productTitle');

            if ($title->count() > 0) {
                $title = $title->first()->text();
            } else
            if ($detailTitle->count() > 0) {
                $title = $detailTitle->first()->text();
            } else {
                $title = '';
            }


            // <a class="btn btn-xs btn-primary" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=funny&linkCode=ur2&tag=anecdotage01-20">Shop for funny</a>

            $title = $title . ' <a href="' . $amazonUrl . '&tag=anecdotage01-20' . '">BUY IT HERE</a>';
            $data = [
                'description'   => $title
            ];
            $this->saveInfo($data);
        }
    }

    /**
     * @param $base64_string
     * @param $output_file
     * @return mixed
     */
    public function base64ToImage($base64_string, $output_file)
    {
        $parts = explode('/', $output_file);
        array_pop($parts);
        $dir = implode('/', $parts);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = fopen($output_file, 'wb');

        $data = explode(',', $base64_string);

        fwrite($file, base64_decode($data[1]));
        fclose($file);

        return $output_file;
    }


    /**
     * @param $data
     */
    public function saveInfo($data)
    {
        // $thread = Thread::where('id', $this->thread->id)->first();
        // $thread->update($data);

        $this->thread->update($data);
    }


    /**
     * @param $url
     * @return mixed
     */
    function getFileExtensionFromURl($url)
    {
        $file = new \finfo(FILEINFO_MIME);
        $type = strstr($file->buffer(file_get_contents($url)), ';', true); //Returns something similar to  image/jpg

        $extension = explode('/', $type)[1];

        return $extension;
    }

    /**
     * @param $fullPath
     * @param $full_image_link
     */
    public function file_download_curl($fullPath, $full_image_link)
    {
        $parts = explode('/', $fullPath);
        array_pop($parts);
        $dir = implode('/', $parts);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fp = fopen($fullPath, 'wb');
        $ch = curl_init($full_image_link);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        fclose($fp);
    }
}
