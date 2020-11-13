<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateThreadAmazonLink implements ShouldQueue
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
    public function handle() {

        dump( $this->thread->description );

        // if(\stripos($this->thread->description, '<a href="https://www.amazon.com/')){
        //         $replaceText ='tag=anecdotage01-20';
        //         $newDescription = str_replace( 'tag=anecdotagecom-20', $replaceText, $this->thread->description );
        // }else{
        //         $keyword = \preg_split('@buy it here@i', $this->thread->description);              
        //         $replaceText = '<a class="btn btn-xs btn-primary" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords='.$keyword[0].'&linkCode=ur2&tag=anecdotage01-20">BUY IT HERE</a>';             
        //         $newDescription = str_replace( 'BUY IT HERE', $replaceText, $this->thread->description );
        // }


        $replaceText = 'tag=anecdotage01-20';
        $newDescription = str_replace( '&anecdotage01-20', $replaceText, $this->thread->description );

        dump( $newDescription );

        $this->thread->description = $newDescription;
        $this->thread->save();
    }
}
