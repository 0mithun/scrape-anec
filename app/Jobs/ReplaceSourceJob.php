<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReplaceSourceJob implements ShouldQueue
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
        $newSource = \str_replace('anecdotagecom-20','anecdotage01-20',$this->thread->source);

        $this->thread->source = $newSource;
       
        $this->thread->save();
    }
}
