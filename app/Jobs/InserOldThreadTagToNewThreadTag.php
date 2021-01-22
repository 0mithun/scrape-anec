<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class InserOldThreadTagToNewThreadTag implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $thread_tag;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($thread_tag)
    {
        $this->thread_tag = $thread_tag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            'thread_id' => $this->thread_tag->thread_id,
            'tag_id' => $this->thread_tag->tag_id,
        ];

        DB::table('thread_tag_new')->insert($data);
    }
}
