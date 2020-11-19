<?php

namespace App\Jobs;

use App\Tags;
use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddBracketsToTagLicense implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tag;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tags $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \dump($this->tag->description);
        $descriptoin = $this->tag->description;

        // $publicSearchText = 'Public domain <a';
        // $publicReplaceText = '(Public domain) <br><a';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

    
        // $publicSearchText = 'CC BY-SA 1.0</a>';
        // $publicReplaceText = '(CC BY-SA 1.0)</a> <br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $publicSearchText = 'CC BY-SA 1.5</a>';
        // $publicReplaceText = '(CC BY-SA 1.5)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $publicSearchText = 'CC BY-SA 2.5 <a';
        // $publicReplaceText = '(CC BY-SA 2.5)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $publicSearchText = 'CC BY-SA 3.0</a>';
        // $publicReplaceText = '(CC BY-SA 3.0)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $publicSearchText = 'CC BY-SA 4.0</a>';
        // $publicReplaceText = '(CC BY-SA 4.0)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);



        // $publicSearchText = 'CC BY 1.0</a>';
        // $publicReplaceText = '(CC BY 1.0)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $publicSearchText = 'CC BY 1.5</a>';
        // $publicReplaceText = '(CC BY 1.5)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $publicSearchText = 'CC BY 2.0</a>';
        // $publicReplaceText = '(CC BY 2.0)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $publicSearchText = 'CC BY 2.5</a>';
        // $publicReplaceText = '(CC BY 2.5)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $publicSearchText = 'CC BY 3.0</a>';
        // $publicReplaceText = '(CC BY 3.0)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $publicSearchText = 'CC BY 4.0</a>';
        // $publicReplaceText = '(CC BY 4.0)</a><br>';
        // $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

        // $error = stripos($descriptoin, '<a');

        // dump($descriptoin);
        
        // if(!$error){
        //     $data = [
        //         'error' => 1,
        //     ];
        // }else{

        //     $data = [
        //         'description' => $descriptoin,
        //     ];
        // }
        // dump($data);
        // dump('-------------');

        // $this->saveInfo( $data );
        if(\strpos($descriptoin, '<a class="btn ')){

            $descriptoin = \str_replace('<a class="btn btn-xs btn-primary" href="http://www.amazon.com','<br><a class="btn btn-xs btn-primary" href="http://www.amazon.com', $descriptoin);
        }else{
            $descriptoin = \str_replace('<a href="http://www.amazon.com','<br><a class="btn btn-xs btn-primary" href="http://www.amazon.com', $descriptoin);
        }

        $descriptoin = str_replace('  ',' ', $descriptoin);
        dump($descriptoin);
        $this->saveInfo(['description'=>$descriptoin]);


    //     $amazonLink = \str_replace('anecdotagecom-20','anecdotage01-20', $descriptoin);

        
    //     $publicSearchText = 'Public domain';
    //     $publicReplaceText = '(Public domain)';
    //     $descriptoin = \str_replace($publicSearchText, $publicReplaceText, $descriptoin);

    //     $sliceAmazon = \substr($descriptoin, stripos($descriptoin, 'http://www.amazon'));
    //     $amazonLink = \str_replace('anecdotagecom-20','anecdotage01-20', $sliceAmazon);
    //     $replace = '&keywords='.$this->tag->name.'&linkCode';
    //     $amazonLink = \str_replace('&keywords=extravagance&linkCode', $replace, $amazonLink);
    //     ///&keywords=extravagance&linkCode
    //     $amazonLink = trim('<br><a href="'.$amazonLink.'">Shop for '.trim($this->tag->name).'</a>'); 

    //     $body =  trim(\substr($descriptoin, 0, stripos($descriptoin, 'http://www.amazon')));
       

       


    //     $license = \substr($descriptoin, stripos($descriptoin, 'CC '));
    //     $licenseText =  trim(\substr($license, 0, stripos($license, ' http:')));
        
    

    //    dump($licenseText);

    //     $saLicenseType = [
    //         'CC BY-SA 1.0',
    //         'CC BY-SA 1.5',
    //         'CC BY-SA 2.5',
    //         'CC BY-SA 3.0',
    //         'CC BY-SA 4.0',
    //     ];
    //     $nonSaLicenseType = [
    //         'CC BY 1.0',
    //         'CC BY 1.5',
    //         'CC BY 2.0 ',
    //         'CC BY 2.5 ',
    //         'CC BY 3.0',
    //         'CC BY 4.0',
    //     ];
    //     if(\in_array($licenseText, $saLicenseType)){
    //         $body = trim(\str_replace($licenseText, '', $body));
    //     }
    //     else if(\in_array($licenseText, $nonSaLicenseType)){
    //         $body = trim(\str_replace($licenseText, '', $body));
    //     }


    //     $htmlLicense = '';

    //     if ( in_array( $licenseText, $saLicenseType ) ) {
    //        if( \preg_match('&(\d)\.?\d?&',$licenseText, $matches)){
    //            $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/'.$matches[0].'">('.$licenseText.')</a>';
    //        }
    //     }else if ( in_array( $licenseText, $nonSaLicenseType ) ) {
    //         if(\preg_match('&(\d)\.?\d?&',$licenseText, $matches)){
    //             $htmlLicense = '<a href="https://creativecommons.org/licenses/by/'.$matches[0].'">('.$licenseText.')</a>';
    //         }
    //     }
        
      

    //         $data = [
    //             'description' => \sprintf("%s %s %s",trim($body," "),trim($htmlLicense),trim($amazonLink)),
    //             'error' => null
    //         ];
        
    //     dump($data);
    //     // dump('-------------');

    //     $this->saveInfo( $data );
    }

    

      /**
     * @param $data
     */
    public function saveInfo( $data ) {
        $this->tag->update( $data );
    }

}
