<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InsertAmazonLink implements ShouldQueue
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
        $pattern  = '%<i>([\w\s]+)</i>%';
        $body = preg_replace_callback($pattern, function ($matches) {
            return '<a target="_blank" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=' . $matches[1] . '&linkCode=ur2&tag=anecdotagecom-20">' . $matches[1] . '</a>';
        }, $this->thread->body);

        $this->thread->update(['body' => $body]);
    }
}
