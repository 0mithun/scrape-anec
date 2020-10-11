<?php

namespace App\Jobs;

use App\Tags;
use App\Thread;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveJunkTags implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    public $tag;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Tags $tag ) {
        $this->tag = $tag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $except = [101454, 101373, 101312, 101038];

        if ( !in_array( $this->tag->id, $except ) ) {
            $threads = $this->tag->threads()->delete();
            $thread_tags = DB::table( 'thread_tag' )->where( 'tag_id', $this->tag->id )->delete();

// if ( count( $threads > 0 ) ) {

//     Thread::whereIn( 'id', $threads )->delete();
            // }

            dump( $this->tag );

            dump( $threads );

            dump( $thread_tags );

// // $threads->delete();

            // $thread_tags->delete();
            $this->tag->delete();
        }

        //
    }

}
