<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StripSlugTagJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    public $thread;
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

        $slug = str_slug( strip_tags( $this->thread->title ) );

        if ( Thread::whereSlug( $slug )->exists() ) {
            $slug = $slug . '-' . $this->thread->id;
        }

        dump( $slug );

        $this->thread->slug = $slug;
        $this->thread->save();

    }

}
