<?php

namespace App\Jobs;

use App\NewTag;
use App\Tags;
use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use phpDocumentor\Reflection\DocBlock\Tag;

class TagImageProcessing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tag;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(NewTag $tag)
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
        //
    }

    public function scrapeWithKeyword()
    {
        $client = new Client();
        $url = 'https://en.wikipedia.org/wiki/' . $this->tag->name;
        $crawler = $client->request('GET', $url);
        $anchor =  $crawler->filter('div.thumbinner a.image')->first();

        $authorText = '';
        $licenseText = '';
        $descriptionText = '';
        $shopText = '';

        if (count($anchor) > 0) {
            $href = $anchor->extract(['href'])[0];
            $image_page_url = 'https://en.wikipedia.org' . $href;
            $this->scrpeImagePageUrl($image_page_url);
        }
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
        $tag = Tags::where('name', strtolower($this->tag->oldnew_tags))->first();

        if (!$tag) {
            Tags::create(['name', strtolower($this->tag->new_tags)]);
        }
    }

    public function scrpeImagePageUrl($image_page_url)
    {
        //
        $client = new Client();
        +$image_page = $client->request('GET', $image_page_url);

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

    public function scrapeFromMediaFile()
    {
        $findUrl = '';
        $image_page_url = '';
        $client = new Client();
        $url = $this->tag->wikipedia_page;
        $crawler = $client->request('GET', $url);

        $thumbinner = $crawler->filter('div.thumbinner');
        $thumbinner->each(function ($node) use ($findUrl, $image_page_url) {
            $anchor = $node->filter('a.image');
            if ($anchor->count() > 0) {
                $href = $anchor->extract(['href'])[0];
                if ($href == $findUrl) {
                    ///wiki/File:Jazzing_orchestra_1921.png
                    $image_page_url = $href;
                }
            }
        });

        if ($image_page_url != '') {
            //go to image page url & extract data;
            $this->scrpeImagePageUrl($image_page_url);
        }
    }

    public function scrapeFromWikifile()
    {
        //

        $this->scrpeImagePageUrl($this->tag->wikipedia_page);
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
        //#/media/File:

        $mediaFile = strpos($this->tag->wikipedia_page, '#/media/File:');
        $wikiFIle = strpos($this->tag->wikipedia_page, 'wiki/File:');

        if ($mediaFile) {
            //scrape from media file
            $this->scrapeFromMediaFile();
        } else if ($wikiFIle) {
            //scrape from wiki file

            $this->scrapeFromWikiFile();
        } else {
            //scrape from wikipedia with keyword
        }
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

        $fileExtension = explode('.', $this->tag->image_link);
        $fileExtension = array_pop($fileExtension);
        $fileName = md5(time() . uniqid());
        $fullFileName = $fileName . '.' . $fileExtension;
        $image_path = 'download/tag/' . $fullFileName;
        $fullPath = 'public/' . $image_path;

        $this->file_download_curl($fullPath, $this->tag->image_link);
        if ($tag) {
            $tag->photo = $fullPath;
            $tag->save();
        } else {
            $newTag = Tags::create(['name' => strtolower($this->tag->old_tag),  'photo' => $fullPath]);
        }
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

    public function file_download_curl($fullPath, $full_image_link)
    {
        $parts = explode('/', $fullPath);
        array_pop($parts);
        $dir = implode('/', $parts);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fp = fopen($fullPath, 'wb');
        $ch = curl_init($full_image_link);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        fclose($fp);
    }
}
