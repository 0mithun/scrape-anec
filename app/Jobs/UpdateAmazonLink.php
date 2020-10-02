<?php

namespace App\Jobs;

use App\Tags;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAmazonLink implements ShouldQueue {
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
        //

        $this->tag = $tag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        dump( $this->tag->description );
        $replaceText = $this->tag->name . '&linkCode=ur2&tag=anecdotagecom-20';
        $newDescription = str_replace( '&linkCode=ur2&tag=anecdotagecom-20', $replaceText, $this->tag->description );

        dump( $newDescription );

        $this->tag->description = $newDescription;
        $this->tag->save();
    }
}
