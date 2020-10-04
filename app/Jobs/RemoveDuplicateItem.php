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

class RemoveDuplicateItem implements ShouldQueue {
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

        $trimTagName = trim( $this->tag->name );

        $findTag = Tags::where( 'name', $trimTagName )->first();

        if ( $findTag ) {
            $thread_tag_ids = DB::table( 'thread_tag' )->where( 'tag_id', $this->tag->id )->get()->pluck( 'thread_id' )->all();
            dump( $thread_tag_ids );

            DB::table( 'thread_tag' )->where( 'tag_id', $this->tag->id )->delete();
            $this->tag->delete();

            foreach ( $thread_tag_ids as $id ) {
                DB::table( 'thread_tag' )->insert( ['tag_id' => $findTag->id, 'thread_id' => $id] );
            }

        } else {
            $this->tag->name = $trimTagName;
            $this->tag->save();
        }

    }

}
