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

class NewNameListScrapingJob implements ShouldQueue
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
        $cnoList = DB::table('cno')->orderBy('id', 'DESC')->get();
        $count = 0;
        foreach ($cnoList as $cnoItem) {
            $count++;
            info($cnoItem->keyword);
            // $title = stripos($this->thread->title, $cnoItem->keyword);
            // $body = strpos($this->thread->body, $cnoItem->keyword);

            if (preg_match("/$cnoItem->keyword/i", $this->thread->title)) {

                if ($cnoItem->found == 1) {
                    dump('found old');
                    $data = [
                        'wiki_image_page_url' => $cnoItem->wiki_image_page_url,
                        'wiki_image_url' => $cnoItem->wiki_image_url,
                        'wiki_image_path' => $cnoItem->wiki_image_url,
                        'wiki_image_path_pixel_color' => $cnoItem->wiki_image_path_pixel_color,
                        'description' => $cnoItem->description

                    ];

                    if (strtoupper($cnoItem->keyword) == 'C') {
                        $data['cno'] = 'C';
                    } else if (strtoupper($cnoItem->keyword) == 'F') {
                        $data['cno'] = 'F';
                    } else {
                        $data['cno'] = 'O';
                    }
                    // dump($data);
                    $this->saveInfo($data);
                } else {
                    $this->scrapeWithKeyword($cnoItem->keyword);
                }

                break;
            } else {
                $tags = $this->thread->tags()->pluck('name')->toArray();
                if (in_array($cnoItem->keyword, $tags)) {
                    dump('found in tags');
                    if ($cnoItem->found == 1) {
                        dump('found old');
                        $data = [
                            'wiki_image_page_url' => $cnoItem->wiki_image_page_url,
                            'wiki_image_url' => $cnoItem->wiki_image_url,
                            'wiki_image_path_pixel_color' => $cnoItem->wiki_image_path_pixel_color,
                            'description' => $cnoItem->description

                        ];

                        if (strtoupper($cnoItem->keyword) == 'C') {
                            $data['cno'] = 'C';
                        } else if (strtoupper($cnoItem->keyword) == 'F') {
                            $data['cno'] = 'F';
                        } else {
                            $data['cno'] = 'O';
                        }
                        // dump($data);
                        $this->saveInfo($data);
                    } else {
                        $this->scrapeWithKeyword($cnoItem->keyword);
                    }

                    break;
                }
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
            // $cnoItem = DB::table('cno')->where('keyword', $originalKeyword)->update(['found' => 2]);
        }
    }


    public function scrpeImagePageUrl($image_page_url, $originalKeyword)
    {
        $client = new Client();
        $licenseText = '';
        $descriptionText = '';

        $authorText = '';
        $htmlLicense = '';
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
                $saLicenseType = [
                    'CC BY-SA 1.0',
                    'CC BY-SA 1.5',
                    'CC BY-SA 2.5',
                    'CC BY-SA 3.0',
                    'CC BY-SA 4.0',
                ];
                $nonSaLicenseType = [
                    'CC BY 1.0',
                    'CC BY 1.5',
                    'CC BY 2.0 ',
                    'CC BY 2.5 ',
                    'CC BY 3.0',
                    'CC BY 4.0',
                ];

                $licenseText = $license->first()->text();
                if ($licenseText == 'Public domain') {
                    $htmlLicense = 'Public domain';
                } else if (in_array($licenseText, $saLicenseType)) {
                    if (\preg_match('&(\d)\.?\d?&', $licenseText, $matches)) {
                        $htmlLicense = '<a href="https://creativecommons.org/licenses/by-sa/' . $matches[0] . '">' . $licenseText . '</a>';
                    }
                } else if (in_array($licenseText, $nonSaLicenseType)) {
                    if (\preg_match('&(\d)\.?\d?&', $licenseText, $matches)) {
                        $htmlLicense = '<a href="https://creativecommons.org/licenses/by/' . $matches[0] . '">' . $licenseText . '</a>';
                    }
                }

                if ($htmlLicense != '') {
                    \dump($htmlLicense);
                } else {
                    \dump('other license');
                }
            }

            $author = $image_page->filter('td#fileinfotpl_aut');

            if ($author->count() > 0) {
                $newAuthor = $image_page->filter('td#fileinfotpl_aut')->nextAll();
                $newAuthor = $newAuthor->filter('a');

                if ($newAuthor->count() > 0) {
                    $authorText = $newAuthor->first()->text();
                }
            }

            $fullDescriptionText = sprintf('%s %s %s', $descriptionText, $authorText, $htmlLicense);
            $pixelColor = $this->getImageColorAttribute($full_image_link);
            $data = [
                'wiki_image_page_url' => $image_page_url,
                'wiki_image_url' => $full_image_link,
                'wiki_image_path' => $full_image_link,
                'wiki_image_path_pixel_color' => $pixelColor ?? '',
                'wiki_image_description' => $fullDescriptionText,
                'description' => $fullDescriptionText

            ];

            if (strtoupper($originalKeyword) == 'C') {
                $data['cno'] = 'C';
            } else if (strtoupper($originalKeyword) == 'F') {
                $data['cno'] = 'F';
            } else {
                $data['cno'] = 'O';
            }

            // dump($data);
            $this->saveInfo($data);


            $cnoItem = DB::table('cno')->where('keyword', $originalKeyword)->update([
                'wiki_image_page_url' => $image_page_url,
                'wiki_image_url' => $full_image_link,
                'wiki_image_path_pixel_color' => $pixelColor ?? '',
                'description' => $fullDescriptionText,
                'found' => 1,
            ]);
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
