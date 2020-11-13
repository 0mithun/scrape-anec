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
        $thread->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // \dump($this->thread->description);
        $descriptoin = $this->thread->description;

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
        
                $pattern = '@\([\s\S][^\)]+\)@';
       
               $licenseText = \preg_match($pattern, $descriptoin, $matches);

               \dump($matches);

               $licenseText = str_replace('(', '', $licenseText);
               $licenseText = str_replace(')', '', $licenseText);

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
        
                
                
                if ( in_array( $licenseText, $saLicenseType ) ) {
                   if( \preg_match('&(\d)\.?\d?&',$licenseText, $matches)){        
                       $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/'.$matches[0].'">('.$licenseText.')</a>';
                   }
                }else if ( in_array( $licenseText, $nonSaLicenseType ) ) {
                    if(\preg_match('&(\d)\.?\d?&',$licenseText, $matches)){        
                        $htmlLicense = '<a href="https://creativecommons.org/licenses/by/'.$matches[0].'">('.$licenseText.')</a>';
                    }
                }

                $htmlDescription = \preg_replace($pattern, $htmlDescription, $descriptoin);

                dump($htmlDescription);
                
                // $data = [
                //     'description' => $descriptoinText.' '.$licenseText,
                // ];
                
                // dump($data);
                // dump('-------------');
        
                // $this->saveInfo( $data );


        }

        

       
    }

     

      /**
     * @param $data
     */
    public function saveInfo( $data ) {
        $this->thread->update( $data );
    }
}
