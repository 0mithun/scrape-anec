<?php

namespace App\Jobs;

use App\Tags;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $this->scrapeTag();
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



    public function saveInfo($data)
    {
        // $tag = Tags::where('id', $this->tag->id)->first();
        // $tag->photo = $image_path;
        // $tag->save();
        $this->tag->update($data);
    }
}
