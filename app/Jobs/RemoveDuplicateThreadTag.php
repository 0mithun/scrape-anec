<?php

namespace App\Jobs;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveDuplicateThreadTag implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    public $threadTag;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $threadTag ) {
        $this->threadTag = $threadTag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $findNewThreadTag = DB::table( 'new_thread_tag' )->where( 'thread_id', $this->threadTag->thread_id )->where( 'tag_id', $this->threadTag->tag_id )->first();

        dump( $findNewThreadTag );

        if ( !$findNewThreadTag ) {
            DB::table( 'new_thread_tag' )->insert( [
                'thread_id' => $this->threadTag->thread_id,
                'tag_id'    => $this->threadTag->tag_id,
            ] );
        }

    }

}
