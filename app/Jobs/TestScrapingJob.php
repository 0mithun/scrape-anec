<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Goutte\Client;

class TestScrapingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $url = 'https://www.yollando.com/en/shipping-calculator-from-turkey/';


        $this->scrpeImagePageUrl($this->url);

    }

    public function scrpeImagePageUrl($image_page_url)
    {
        $client = new Client();
        $licenseText = '';

        $authorText = '';
        $htmlLicense = '';
        $descriptionText = '';


        $image_page = $client->request('GET', $image_page_url);
        $text =    $image_page->text();

        $license = $this->checkLicense($text);


        dump($image_page_url);
        dump($license);


        // $license = $image_page->filter('table.licensetpl span.licensetpl_short');

        // if ($license->count() > 0) {
        //     $saLicenseType = [
        //         'CC BY-SA 1.0',
        //         'CC BY-SA 1.5',
        //         'CC BY-SA 2.0',
        //         'CC BY-SA 2.5',
        //         'CC BY-SA 3.0',
        //         'CC BY-SA 4.0',
        //     ];
        //     $nonSaLicenseType = [
        //         'CC BY 1.0',
        //         'CC BY 1.5',
        //         'CC BY 2.0',
        //         'CC BY 2.5',
        //         'CC BY 3.0',
        //         'CC BY 4.0',
        //     ];

        //     $licenseText = $license->first()->text();
        //     if ($licenseText == 'Public domain') {
        //         $htmlLicense = 'Public domain';
        //     } else if (in_array($licenseText, $saLicenseType)) {
        //         if (\preg_match('&(\d)\.?\d?&', $licenseText, $matches)) {
        //             $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/' . $matches[0] . '">' . $licenseText . '</a>';
        //         }
        //     } else if (in_array($licenseText, $nonSaLicenseType)) {
        //         if (\preg_match('&(\d)\.?\d?&', $licenseText, $matches)) {
        //             $htmlLicense = '<a href="https://creativecommons.org/licenses/by/' . $matches[0] . '">' . $licenseText . '</a>';
        //         }
        //     }

        //     if ($htmlLicense != '') {
        //         \dump($htmlLicense);
        //     } else {
        //         \dump('other license');
        //     }
        // }else{
        //     $found =  $image_page->filterXPath("//*[text() = 'CC BY 3.0']");

        //     dd($found);
        // }

    }

    public function checkLicense($text){
         $htmlLicense = '';
            $saLicenseType = [
                'CC BY-SA 1.0',
                'CC BY-SA 1.5',
                'CC BY-SA 2.0',
                'CC BY-SA 2.5',
                'CC BY-SA 3.0',
                'CC BY-SA 4.0',
            ];
            $nonSaLicenseType = [
                'CC BY 1.0',
                'CC BY 1.5',
                'CC BY 2.0',
                'CC BY 2.5',
                'CC BY 3.0',
                'CC BY 4.0',
            ];
            $matches = false;

            foreach ($saLicenseType as $license) {
                $pattern = "/$license/";
                if(preg_match($pattern ,$text)){
                    $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/'.$license.'">' . $license . '</a>';
                    $matches = true;
                    break;
                }
            }

            if($matches == false){
                foreach ($nonSaLicenseType as $license) {
                    $pattern = "/$license/";
                    if(preg_match($pattern ,$text)){
                        $htmlLicense = '<a href="https://creativecommons.org/licenses/by/'.$license.'">' . $license . '</a>';
                        $matches = true;
                        break;
                    }
                }
            }

       return $htmlLicense;
    }
}

 public function checkLicense($image_page){

        $text =    $image_page->text();
         $htmlLicense = '';
            $saLicenseType = [
                'CC BY-SA 1.0',
                'CC BY-SA 1.5',
                'CC BY-SA 2.0',
                'CC BY-SA 2.5',
                'CC BY-SA 3.0',
                'CC BY-SA 4.0',
            ];
            $nonSaLicenseType = [
                'CC BY 1.0',
                'CC BY 1.5',
                'CC BY 2.0',
                'CC BY 2.5',
                'CC BY 3.0',
                'CC BY 4.0',
            ];
            $matches = false;

            foreach ($saLicenseType as $license) {
                $pattern = "/$license/";
                if(preg_match($pattern ,$text)){
                    $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/'.$license.'">' . $license . '</a>';
                    $matches = true;
                    break;
                }
            }

            if($matches == false){
                foreach ($nonSaLicenseType as $license) {
                    $pattern = "/$license/";
                    if(preg_match($pattern ,$text)){
                        $htmlLicense = '<a href="https://creativecommons.org/licenses/by/'.$license.'">' . $license . '</a>';
                        $matches = true;
                        break;
                    }
                }
            }

       return $htmlLicense;
    }
