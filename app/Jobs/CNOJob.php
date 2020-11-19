<?php

namespace App\Jobs;

use App\Tags;
use App\Thread;

use Goutte\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use DB;

class CNOJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $thread;

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
        $cnoList = DB::table('cno')->get();
        $count = 0;
        foreach ($cnoList as $cnoItem) {
            $count++;
            info($cnoItem->keyword);
            $title = strpos($this->thread->title, $cnoItem->keyword);
            $body = strpos($this->thread->body, $cnoItem->keyword);
            if ($title || $body) {
                info("Found: " . $cnoItem->keyword);
                $this->thread->cno = strtoupper($cnoItem->cno);
                $this->thread->save();

                if (strtoupper($cnoItem->cno) == 'C') {
                    $tag = Tags::where('name', 'celebrities')->first();
                    if ($tag) {
                        $findTag =  DB::table('thread_tag')->where('thread_id', $this->thread->id)->where('tag_id', $tag->id)->first();
                        if (!$findTag) {
                            DB::table('thread_tag')->insert(['thread_id' => $this->thread->id, 'tag_id' => $tag->id]);
                        }
                    }
                }
                if ($this->thread->image_path == '' && $this->thread->amazon_image_path == '' && $this->thread->other_image_path == '' && $this->thread->wiki_image_path == '') {
                    //scrape image from wikipedia & set pixel color



                    //Check db if exists save from here otherwise scrape

                    if ($cnoItem->found == 0) {
                        $this->scrapeWithKeyword($cnoItem->keyword);
                    } else if ($cnoItem->found == 1) {
                        $data = [
                            'wiki_image_page_url' => $cnoItem->wiki_image_page_url,
                            'wiki_image_url' => $cnoItem->wiki_image_url,
                            'wiki_image_path' => $cnoItem->wiki_image_path,
                            'wiki_image_path_pixel_color' => $cnoItem->wiki_image_path_pixel_color,
                            'wiki_image_description' => $cnoItem->wiki_image_description,
                        ];

                        $this->saveInfo($data);
                    }
                }
                break;
            }
        }

        info("Total: " . $count);
    }
    public function scrapeWithKeyword($keyword)
    {
        $originalKeyword = $keyword;
        $keyword = ucwords($keyword);
        $keyword = str_replace(' ', '_', $keyword);
        $newUrl = "https://en.wikipedia.org/wiki" . '/' . $keyword;

        $client = new Client();
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


        if (isset($image_page_url)) {

            $this->scrpeImagePageUrl($image_page_url, $originalKeyword);
        } else {
            $cnoItem = DB::table('cno')->where('keyword', $originalKeyword)->update(['found' => 2]);
        }
    }


    public function scrpeImagePageUrl($image_page_url, $originalKeyword)
    {
        $client = new Client();
        $licenseText = '';
        $descriptionText = '';

        $image_page = $client->request('GET', $image_page_url);

        if ($image_page->filter('.fullImageLink a')->count() > 0) {
            $full_image_link =  $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
            $full_image_link = str_replace('//upload', 'upload', $full_image_link);
            $full_image_link = 'https://' . $full_image_link;
            $full_image_link =  str_replace("//https:", '//', $full_image_link);
        }

        if (isset($full_image_link)) {
            $description = $image_page->filter('div.description');
            if ($description->count() > 0) {
                $description =  $description->first()->text();
                $descriptionText = str_replace('English: ', '', $description);
            }
            $license = $image_page->filter('table.licensetpl span.licensetpl_short');
            if ($license->count() > 0) {
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

            $pixelColor = $this->getImageColorAttribute($full_image_link);

            $data = [
                'wiki_image_page_url' => $image_page_url,
                'wiki_image_url' => $full_image_link,
                'wiki_image_path' => $full_image_link,
                'wiki_image_path_pixel_color' => $pixelColor ?? '',
                'wiki_image_description' => $descriptionText . $licenseText,

            ];

            $this->saveInfo($data);
            $data['found'] = 1;
            $cnoItem = DB::table('cno')->where('keyword', $originalKeyword)->update($data);
            // $cnoItem->found = 1;
            // $cnoItem->save();
        }
    }

    public function getImageColorAttribute($image_path)
    {
        if ($image_path != '') {
            $splitName = explode('.', $image_path);
            $extension = strtolower(array_pop($splitName));

            if ($extension == 'jpg') {
                $im = imagecreatefromjpeg($image_path);
            }
            if ($extension == 'jpeg') {
                $im = imagecreatefromjpeg($image_path);
            } else if ($extension == 'png') {
                $im = imagecreatefrompng($image_path);
            } else if ($extension == 'gif') {
                $im = imagecreatefromgif($image_path);
            }

            if (isset($im)) {
                $rgb = imagecolorat($im, 0, 0);
                $colors = imagecolorsforindex($im, $rgb);
                array_pop($colors);
                array_push($colors, 1);
                $rgbaString = join(', ', $colors);

                return $rgbaString;
            }
        }
        return false;
    }

    public function saveInfo($data)
    {
        $this->thread->update($data);
    }
}
