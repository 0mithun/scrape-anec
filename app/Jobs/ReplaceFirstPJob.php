<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReplaceFirstPJob implements ShouldQueue
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
        // $pattern  = '#^</p>#';
        // $newBody = \preg_replace($pattern,'',$this->thread->body);
        // dump($this->thread->body);
        
        $pattern  = '#^<p>([^</p]+)</p>#';
        \preg_match_all($pattern, $this->thread->body, $matches);
        \dump($matches);
                
        $newBody = preg_replace($pattern,$matches[1][0], $this->thread->body);

        // dump($this->thread->body);
        // dump($newBody);



        // $newBody = \preg_replace($pattern,'',$this->thread->body);
        // dump($this->thread->body);
        


        // \dump($newBody);
        
        $this->thread->body = $newBody;
        $this->thread->save();
        dump("------------------------------");
    }
}
