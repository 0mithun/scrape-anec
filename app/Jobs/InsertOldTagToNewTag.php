<?php

namespace App\Jobs;

use App\Tags;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class InsertOldTagToNewTag implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tag;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tags $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            'old_id' => $this->tag->id,
            'name' => $this->tag->name,
            'slug' => str_slug($this->tag->name),
            'photo' => $this->tag->photo,
            'description' => $this->tag->description,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('tags_new')->insert($data);
    }
}
