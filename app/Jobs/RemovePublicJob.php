<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemovePublicJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $thread;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Thread $thread ) {
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // $this->removePublicFromAmazonPath();
        $other_image_path = str_replace( 'public/', '', $this->thread->other_image_path );
        $this->thread->other_image_path = $other_image_path;
        $this->thread->save();
    }
}
