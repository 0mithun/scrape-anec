<?php

namespace App\Jobs;

use App\Tags;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;

class TagImageProcessing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tag;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tag)
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
        // $this->scrapeTag();
        // if ($this->tag->name != null) {
        //     $client = new Client();
        //     $url = 'https://en.wikipedia.org/wiki/' . $this->tag->name;

        //     $shopLink = "http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={$this->tag->name}&linkCode=ur2&tag=anecdotagecom-20";
        //     // Description has 4 parts
        //     // a) image info
        //     // b) author
        //     // c) license
        //     // d) [shop] link

        //     // Suppose image is for tag = monkeys

        //     // a) image info
        //     // Try to scrape, else use tag text:
        //     // Monkeys

        //     // b) author
        //     // Try to scrape, else null

        //     // c) license
        //     // Try to scrape, else null

        //     // d) [shop] link
        //     // Try to scrape, else use
        //     // http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=TAGTEXT
        //     // &linkCode=ur2&tag=anecdotagecom-20




        //     // Hey i forgot something important...
        //     // Image description should also show AUTHOR

        //     // Example
        //     // https://en.wikipedia.org/wiki/Lumber#/media/File:The_longest_board_in_the_world_(2002).jpg

        //     // Author is "Piotr J"

        //     // Our photo description should say:
        //     // "The longest plank in the world (2002) is in Poland and measures 36.83 metres (about 120 ft 10 in) long. Wikipedia photo: Piotr J (CC BY-SA 3.0)"

        //     // All of our image descriptions should be like:
        //     // [Scraped description]. Photo by: [scraped author] ([scraped license type])
        //     // EG:
        //     // "The longest plank in the world (2002) is in Poland and measures 36.83 metres (about 120 ft 10 in) long. Wikipedia photo: Piotr J (CC BY-SA 3.0)"
        //     // Use:
        //     // "The longest plank in the world (2002) is in Poland and measures 36.83 metres (about 120 ft 10 in) long. Wikipedia photo (CC BY-SA 3.0)"
        //     // Just say "Wikipedia photo" + license type


        //     $data = [];

        //     // $this->saveInfo($data);

        // }
    }

    public function scrapeTag()
    {
        $client = new Client();
        $url = 'https://en.wikipedia.org/wiki/' . $this->tag;
        $crawler = $client->request('GET', $url);
        $anchor =  $crawler->filter('div.thumbinner a.image')->first();

        $authorText = '';
        $licenseText = '';
        $descriptionText = '';
        $shopText = '';

        if (count($anchor) > 0) {
            $href = $anchor->extract(['href'])[0];
            $image_page_url = 'https://en.wikipedia.org' . $href;
            $image_page = $client->request('GET', $image_page_url);

            if ($image_page->filter('.fullImageLink a')->count() > 0) {

                $full_image_link =  $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
                $full_image_link = str_replace('//upload', 'upload', $full_image_link);
                $full_image_link = 'https://' . $full_image_link;
            }

            $description = $image_page->filter('td.description');
            if ($description->count() > 0) {
                $description =  $description->first()->text();
                $descriptionText = str_replace('English: ', '', $description);
            }


            $license = $image_page->filter('table.licensetpl span.licensetpl_short');
            if ($license->count() > 0) {
                //GFDL
                $licenseText = $license->first()->text();
            }

            $author = $image_page->filter('td#fileinfotpl_aut');

            if ($author->count() > 0) {
                $newAuthor = $image_page->filter('td#fileinfotpl_aut')->nextAll();
                $newAuthor = $newAuthor->filter('a');
                if ($newAuthor->count() > 0) {
                    $authorText =  $newAuthor->first()->text();
                }
            }

            info($this->tag);
            info($full_image_link);
            info($descriptionText);
            info($licenseText);
            info($authorText);
        }
    }

    public function attachTags()
    {
    }

    /**
     * Replace tag
     * For each thread with old tag (COL B): add ALL the new tags (COL C, comma delimited) & delete old tag. Create new tag(s) if they don't exist. Destroy old tag.
     */

    public function taskR()
    {
        $old_tag = Tags::where('name', strtolower($this->tag->name))->first();
        $newTag = $this->tag->new_tag;
        $splitNewTag = explode(',', $newTag);

        if ($old_tag) {
            $threads = $old_tag->threads;

            $threads->echo(function ($thread) use ($old_tag) {
                $thread->tags()->detach($old_tag->id);
            });
            $old_tag->delete();

            if (count($splitNewTag) > 0) {
                $tag_ids = [];
                foreach ($splitNewTag as $tag) {
                    $searchTag  = Tags::where('name', strtolower($tag))->first();
                    if ($searchTag) {
                        $tag_ids[] = $searchTag->id;
                    } else {
                        $newTag = Tags::create(['name' => strtolower($tag)]);
                        $tag_ids[] = $newTag->id;
                    }
                }
                $threads->each(function ($thread) use ($tag_ids) {
                    $thread->tags()->syncWithoutDetaching($tag_ids);
                });
            }
        }
    }

    /**
     * task=a: add
     * Just add new tag (none of these rows have images).
     */
    public function taskA()
    {
        $tag = Tags::where('name', strtolower($this->tag->old_tag))->first();

        if (!$tag) {
        }
    }

    /**
     *
     * task=w: Wikipedia scraping
     * All of these have a value for wikipedia page (COL D).
     * Scrape image, description, & license type from wikipedia (like before)
     * If tag is new, add it.
     * Use first image from wikipedia page as tag image. (Don't need to store these images, can hot link)
     *
     */

    public function taskW()
    {
    }

    /**
     * task=i: get image (from www link)
     * Use image link (COL E) to get tag image.
     * If tag is new, add it.
     * Also need a description (see below)
     */

    public function taskI()
    {
        $tag = Tags::where('name', strtolower($this->tag->old_tag))->first();
        // if ($tag) {
        //     $threadTags = DB::table('thread_tag')->where('tag_id', $tag->id)->delete();

        //     $tag->delete();
        // }
    }

    /**
     * task=d: delete
     * Delete old tag
     */

    public function taskD()
    {
        $tag = Tags::where('name', $this->tag->old_tag)->first();
        if ($tag) {
            $threadTags = DB::table('thread_tag')->where('tag_id', $tag->id)->delete();

            $tag->delete();
        }
    }

    public function saveInfo($data)
    {
        // $tag = Tags::where('id', $this->tag->id)->first();
        // $tag->photo = $image_path;
        // $tag->save();
        $this->tag->update($data);
    }
}
