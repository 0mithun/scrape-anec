<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddOldLikeToNewLike implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $like;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($like)
    {
        $this->like = $like;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            'user_id'   =>  $this->like->user_id,
            'likeable_id'   =>  $this->like->likeable_id,
            'likeable_type'   =>  'App\Models\Thread',
            'vote_type'   =>  $this->like->up == 1 ? 'UP' : 'DOWN',
            'created_at'   =>  now(),
            'updated_at'   =>  now(),
        ];

        DB::table('likes_new')->insert($data);
    }
}
