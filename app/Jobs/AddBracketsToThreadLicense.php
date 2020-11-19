<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddBracketsToThreadLicense implements ShouldQueue
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
        $descriptoin = $this->thread->description;
        dump($descriptoin);
        
                $pattern = '@\([\s\S][^\)]+\)@';
       
               $licenseText = \preg_match($pattern, $descriptoin, $matches);

               $licenseText = str_replace(')', '', $matches[0]);
                $licenseText = str_replace('(', '', $licenseText);
                dump($licenseText);

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
                $htmlLicense = '';
               
                if( $licenseText == 'Public domain'){
                    $htmlLicense = ' (Public domain)';
                }
                
               else if ( in_array( $licenseText, $saLicenseType ) ) {
                   if( \preg_match('&(\d)\.?\d?&',$licenseText, $matches)){        
                       $htmlLicense = ' <a href="https://creativecommons.org/licenses/by-sa/'.$matches[0].'">('.$licenseText.')</a>';
                       dump($htmlLicense);
                   }
                }else if ( in_array( $licenseText, $nonSaLicenseType ) ) {
                    if(\preg_match('&(\d)\.?\d?&',$licenseText, $matches)){        
                        $htmlLicense = ' <a href="https://creativecommons.org/licenses/by/'.$matches[0].'">('.$licenseText.')</a>';
                        dump($htmlLicense);
                    }
                }

                if($htmlLicense != ''){

                    $htmlDescription = \preg_replace($pattern, $htmlLicense, $descriptoin);
    
                    dump($htmlDescription);
                    
                    $data = [
                        'description' => $htmlDescription
                    ];
                    
                    dump($data);
                    $this->saveInfo( $data );
                }



        }

        

       
    

     

      /**
     * @param $data
     */
    public function saveInfo( $data ) {
        $this->thread->update( $data );
    }
}
