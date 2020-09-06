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
        // if ($this->tag->task == 'd') {
        //     $this->taskD();
        // } else if ($this->tag->task == 'r') {
        //     $this->taskR();
        // } else if ($this->tag->task == 'a') {
        //     $this->taskA();
        // } else if ($this->tag->task == 'w') {
        //     $this->taskW();
        // } else if ($this->tag->task == 'i') {
        //     $this->taskI();
        // }
        $this->scrapeWithKeyword();
    }

    public function scrapeWithKeyword()
    {

        // $splitKeyword = explode('/', $this->tag->wikipedia_page);


        // $keyword = array_pop($splitKeyword);
        $keyword = $this->tag->name;

        $keyword = ucwords($keyword);
        $keyword = str_replace(' ', '_', $keyword);
        // $newUrl = implode('/', $splitKeyword) . '/' . $keyword;
        $newUrl = "https://en.wikipedia.org/wiki" . '/' . $keyword;

        $client = new Client();
        // $url = 'https://en.wikipedia.org/wiki/' . $this->tag->name;
        $crawler = $client->request('GET', $newUrl);

        $infobox = $crawler->filter('table.infobox a.image')->first();

        if (count($infobox)) {
            $href = $infobox->extract(['href'])[0];
            $image_page_url = 'https://en.wikipedia.org' . $href;
        } else {
            $thumbinner =  $crawler->filter('div.thumbinner a.image')->first();
            if (count($thumbinner) > 0) {
                $href = $thumbinner->extract(['href'])[0];
                $image_page_url = 'https://en.wikipedia.org' . $href;
            }
        }
        // dump($newUrl);
        // dump($image_page_url);
        $this->scrpeImagePageUrl($image_page_url);
    }


    /**
     * Test OK
     * Replace tag
     * For each thread with old tag (COL B): add ALL the new tags (COL C, comma delimited) & delete old tag. Create new tag(s) if they don't exist. Destroy old tag.
     */

    public function taskR()
    {
        $newTags = $this->tag->new_tags;
        $splitNewTag = explode(',', $newTags);

        $old_tag = Tags::where('name', strtolower($this->tag->old_tag))->first();
        // dump('Old Tag', $old_tag);
        if ($old_tag) {
            $threads = $old_tag->threads;

            $threads->each(function ($thread) use ($old_tag) {
                $thread->tags()->detach($old_tag->id);
            });
            $old_tag->delete();

            dump('Deleted Old Tag', $this->tag->old_tag);


            if (count($splitNewTag) > 0) {
                dump($newTags);
                dump($splitNewTag);
                $tag_ids = [];
                foreach ($splitNewTag as $tag) {
                    $searchTag  = Tags::where('name', strtolower($tag))->first();
                    dump('Search Tag', $searchTag);
                    if ($searchTag) {
                        $tag_ids[] = $searchTag->id;
                    } else {
                        $newTag = Tags::create(['name' => strtolower($tag)]);
                        $tag_ids[] = $newTag->id;
                        dump('New Tags', $newTag);
                        info($newTag);
                    }
                }
                $threads->each(function ($thread) use ($tag_ids) {
                    $thread->tags()->syncWithoutDetaching($tag_ids);
                });
            }
        } else {
            info('Old tag not found');
            foreach ($splitNewTag as $tag) {
                $searchTag  = Tags::where('name', strtolower($tag))->first();
                // dump('Search Tag', $searchTag);
                if ($searchTag) {
                    // $tag_ids[] = $searchTag->id;
                } else {
                    $newTag = Tags::create(['name' => strtolower($tag)]);
                    // $tag_ids[] = $newTag->id;
                    // dump('New Tags', $newTag);
                    info($newTag);
                }
            }
        }
    }

    /**
     * Test Ok
     * task=a: add
     * Just add new tag (none of these rows have images).
     */
    public function taskA()
    {
        $tag = Tags::where('name', strtolower($this->tag->new_tags))->first();
        dump($this->tag);
        dump($tag);
        if (!$tag) {
            Tags::create(['name', strtolower($this->tag->new_tags)]);
        }
    }

    public function scrpeImagePageUrl($image_page_url)
    {
        $client = new Client();

        $authorText = '';
        $licenseText = '';
        $descriptionText = '';
        $shopText =  "http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={$this->tag->name}&linkCode=ur2&tag=anecdotagecom-20";

        $image_page = $client->request('GET', $image_page_url);

        if ($image_page->filter('.fullImageLink a')->count() > 0) {
            $full_image_link =  $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
            $full_image_link = str_replace('//upload', 'upload', $full_image_link);
            $full_image_link = 'https://' . $full_image_link;
        }

        if (isset($full_image_link)) {

            $description = $image_page->filter('div.description');
            if ($description->count() > 0) {
                $description =  $description->first()->text();
                $descriptionText = str_replace('English: ', '', $description);
            }
            $license = $image_page->filter('table.licensetpl span.licensetpl_short');
            if ($license->count() > 0) {
                //GFDL
                // $licenseText = $license->first()->text();

                $acceptedLicenses = [
                    'CC BY-SA 1.0',
                    'CC BY-SA 1.5',
                    'CC BY 1.0',
                    'CC BY 1.5',
                    'CC BY-SA 2.5',
                    'CC BY 2.0 ',
                    'CC BY 2.5 ',
                    'CC BY-SA 3.0',
                    'CC BY 3.0',
                    'Public domain',
                    'CC BY-SA 4.0',
                    'CC BY 4.0',
                ];

                if (in_array($license->first()->text(), $acceptedLicenses)) {
                    $licenseText = $license->first()->text();
                }
            }
            $author = $image_page->filter('td#fileinfotpl_aut');

            if ($author->count() > 0) {
                $newAuthor = $image_page->filter('td#fileinfotpl_aut')->nextAll();
                $newAuthor = $newAuthor->filter('a');
                if ($newAuthor->count() > 0) {
                    $authorText =  $newAuthor->first()->text();
                }
            }
            $fullDescriptionText = sprintf("%s %s %s %s", $descriptionText, $authorText, $licenseText, $shopText);
            // info($this->tag);
            // info($full_image_link);
            // info($descriptionText);
            // info($license->first()->text());
            // info($licenseText);
            // info($authorText);

            // info($fullDescriptionText);

            $data = [
                'photo' =>  $full_image_link,
                'description' =>  $fullDescriptionText,
            ];

            $this->saveInfo($data);
        }
    }

    public function scrapeFromMediaFile()
    {

        $findUrl = '';
        $image_page_url = '';
        $split_url = explode('media/', $this->tag->wikipedia_page);
        if (count($split_url) > 1) {
            $image_page_url = 'https://en.wikipedia.org/wiki/' . $split_url[1];
        }

        // dump($image_page_url);
        // $client = new Client();
        // $url = $this->tag->wikipedia_page;
        // $crawler = $client->request('GET', $url);

        // $thumbinner = $crawler->filter('div.thumbinner');
        // $thumbinner->each(function ($node) use ($findUrl, $image_page_url) {
        //     $anchor = $node->filter('a.image');
        //     if ($anchor->count() > 0) {
        //         $href = $anchor->extract(['href'])[0];
        //         if ($href == $findUrl) {
        //             ///wiki/File:Jazzing_orchestra_1921.png
        //             $image_page_url = $href;
        //             dump($image_page_url);
        //             dump($href);
        //         }
        //     }
        // });


        if ($image_page_url != '') {
            //go to image page url & extract data;
            $this->scrpeImagePageUrl($image_page_url);
        }
    }

    public function scrapeFromWikifile()
    {
        $this->scrpeImagePageUrl($this->tag->wikipedia_page);
    }
    public function scrapeFromArchivo()
    {
        $this->scrpeImagePageUrl($this->tag->wikipedia_page);
    }

    public function scrapeFromEncodedUrl()
    {
        $this->scrpeImagePageUrl($this->tag->wikipedia_page);
    }

    public function scrapeFromDosiro()
    {
        $this->scrpeImagePageUrl($this->tag->wikipedia_page);
    }

    public function scrapeFromMediaArchivo()
    {
        //#/media/Archivo:

        $image_page_url = '';
        $split_url = explode('#/media/Archivo:', $this->tag->wikipedia_page);
        if (count($split_url) > 1) {
            $image_page_url = 'https://en.wikipedia.org/wiki/' . $split_url[1];
        }
        if ($image_page_url != '') {
            //go to image page url & extract data;
            $this->scrpeImagePageUrl($image_page_url);
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
        $mediaFile = strpos($this->tag->wikipedia_page, '#/media/File:');
        $wikiFIle = strpos($this->tag->wikipedia_page, 'wiki/File:');
        $archiver = strpos($this->tag->wikipedia_page, 'wiki/Archivo:');
        $encodeImagePageUrl = strpos($this->tag->wikipedia_page, 'wiki/File%3');
        $Dosiero = strpos($this->tag->wikipedia_page, 'wiki/Dosiero:');
        $mediaArchivo = strpos($this->tag->wikipedia_page, '#/media/Archivo:');
        $wikiFail = strpos($this->tag->wikipedia_page, 'wiki/Fail:');


        if ($mediaFile) {
            //scrape from media file
            $this->scrapeFromMediaFile();
        } else if ($wikiFIle) {
            $this->scrapeFromWikiFile();
        } else if ($archiver) {
            $this->scrapeFromArchivo();
        } else if ($encodeImagePageUrl) {
            $this->scrapeFromEncodedUrl();
        } else if ($Dosiero) {
            $this->scrapeFromDosiro();
        } else if ($mediaArchivo) {
            $this->scrapeFromMediaArchivo();
        } else if ($wikiFail) {
            //
            $this->scrpeImagePageUrl($this->tag->wikipedia_page);
        } else {
            //scrape from wikipedia with keyword
            $this->scrapeWithKeyword();
        }
    }

    /**
     * Test Ok
     * task=i: get image (from www link)
     * Use image link (COL E) to get tag image.
     * If tag is new, add it.
     * Also need a description (see below)
     */

    public function taskI()
    {
        $tag = Tags::where('name', strtolower($this->tag->new_tags))->first();

        // $fileExtension = explode('.', $this->tag->image_link);
        // $fileExtension = array_pop($fileExtension);
        $fileExtension = $this->getFileExtensionFromURl($this->tag->image_link);

        $fileName = md5(time() . uniqid());
        $fullFileName = $fileName . '.' . $fileExtension;
        $image_path = 'download/tag/' . $fullFileName;
        $fullPath = 'public/' . $image_path;

        $description = "{$tag->name} " . "http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={$this->tag->new_tags}&linkCode=ur2&tag=anecdotagecom-20";

        $this->file_download_curl($fullPath, $this->tag->image_link);
        if ($tag) {
            $tag->description = $description;
            $tag->photo = $fullPath;
            $tag->save();
        } else {
            $newTag = Tags::create(['name' => strtolower($this->tag->new_tags),  'photo' => $fullPath, 'description' => $description]);
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
            $threadTags = DB::table('thread_tag')->where('tag_id', $tag->id)->get(); //23096 -15421
            info($threadTags);
            dump($threadTags);

            DB::table('thread_tag')->where('tag_id', $tag->id)->delete();

            $tag->delete();
        }
    }

    public function saveInfo($data)
    {
        // $tag = Tags::where('name', $this->tag->new_tags)->first();
        // if ($tag) {
        //     $tag->update($data);
        // } else {
        //     $tag = Tags::create([
        //         'name'  => $this->tag->new_tags,
        //         'photo' => $data['photo'],
        //         'description' => $data['description'],
        //     ]);

        //     info('Creating tag', $tag->name);
        // }
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


    function getFileExtensionFromURl($url)
    {
        $file = new \finfo(FILEINFO_MIME);
        $type = strstr($file->buffer(file_get_contents($url)), ';', true); //Returns something similar to  image/jpg

        $extension = explode('/', $type)[1];

        return $extension;
    }
}
