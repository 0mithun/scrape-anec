<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTagNames implements ShouldQueue
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
        $tags = $this->thread->tags->pluck('name')->all();
        if (count($tags) > 0) {
            $tagNameList = implode(',', $tags);

            $this->thread->update(['tag_names' => $tagNameList]);
        }
    }
}
