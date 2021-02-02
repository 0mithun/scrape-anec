<?php

namespace App\Jobs;

use App\NewThreads;
use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class InsertOldDbToNewDB implements ShouldQueue
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
        $data = [
            'old_id'    => $this->thread->id,
            'user_id'    => $this->thread->user_id,
            'channel_id'    => $this->thread->channel_id,
            'slug'    => $this->thread->slug,
            'title'    => $this->thread->title,
            'body'    => $this->thread->body,
            'summary'    => $this->thread->summary,
            'source'    => $this->thread->source,
            'main_subject'    => $this->thread->main_subject,



            'temp_image_url'    => null, //
            'temp_image_description'    => null, //
            'image_saved'    => 1, //
            'cno'    => $this->thread->cno,
            'age_restriction'    => $this->thread->age_restriction,
            'anonymous'    => $this->thread->anonymous,


            'formatted_address'    => $this->thread->location, //



            'is_published'    => $this->thread->is_published,
            // 'famous'    => $this->thread->famous,
            'visits'    => $this->thread->visits,
            'favorite_count'    => $this->thread->favorite_count,
            'like_count'    => $this->thread->like_count,
            'dislike_count'    => $this->thread->dislike_count,
            'slide_body'    => $this->thread->slide_body,
            'slide_image_pos'    => $this->thread->slide_image_pos,
            'slide_color_bg'    => $this->thread->slide_color_bg,
            'slide_color_0'    => $this->thread->slide_color_0,
            'slide_color_1'    => $this->thread->slide_color_1,
            'slide_color_2'    => $this->thread->slide_color_2
        ];

        if ($this->thread->amazon_image_path && $this->thread->amazon_image_path != '') {
            $data['image_path'] = $this->thread->amazon_image_path;
            $data['image_path_pixel_color'] = $this->thread->amazon_image_path_pixel_color;
        } else if ($this->thread->other_image_path && $this->thread->other_image_path != '') {
            $data['image_path'] = $this->thread->other_image_path;
            $data['image_path_pixel_color'] = $this->thread->other_image_path_pixel_color;
        } else if ($this->thread->wiki_image_path && $this->thread->wiki_image_path != '') {
            $data['image_path'] = $this->thread->wiki_image_path;
            $data['image_path_pixel_color'] = $this->thread->wiki_image_path_pixel_color;
        } else {
            $data['image_path'] = $this->thread->image_path;
            $data['image_path_pixel_color'] = $this->thread->image_path_pixel_color;
        }

        if ($this->thread->lat != null && $this->thread->lng != null) {
            $data['location']    =  new Point((float) $this->thread->lat, (float) $this->thread->lng);
        }

        if ($this->thread->description != null) {
            $data['image_description'] = $this->thread->description;
        } else {
            $data['image_description'] = $this->thread->wiki_image_description;
        }


        NewThreads::create($data);
    }
}
