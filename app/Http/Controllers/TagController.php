<?php

namespace App\Http\Controllers;

use App\Jobs\TagImageProcessing;
use Goutte\Client;
use App\Tags;

class TagController extends Controller
{
    public $tags = [
        'ted rogers',
        'amazing',
        'speech-language pathology',
        'small',
        'sounds bad',
        'anecdotes',
        'unpopularity',
        'appetite',
        'appetites',
        'ruthlessness',
        'sticklers',
        'utility',
        'genitals',
        'artificiality',
        'provisions',
        'ingratitude',
        'refusals',
        'disagreement',
        'discouragement',
        'technicalities',
        'takeovers',
        'presumption',
        'appetite',
        'principles',
        'players',
        'accomplishment',
        'alibis',
        'origins',
        'personas',
        'soho',
        'territory',
        'treaties',
        'graft',
        'curfews',
        'translation',
        'prima donnas',
        'rarity',
        'character',
        'techniques',
        'welching',
        'inaccuracies',
        'integration',
        'hmos',
        'quotas',
        'financial transactions',
        'prognoses',
        'visual jokes',
        'mutual funds',
        'special interests',
        'icons',
        'open-mindedness',
        'steward orme',
        'misnomers',
        'old',
        'trl',
        'subsidies',
        'sincerity',
        'growth',
        'partisanship',
        'exemptions',
        'michael brown',
        'mayors',
        'inquiries',
        'home',
        'deferments',
        'approval',
        'bases',
        'premature ejaculation',
        'ebonics',
        'compatibility',
        'camp',
        'partners',
        'useless',
        'flights',
        'size',
        'older men',
        'pop-ups',
        'therapists',
        'breaks',
        'technique',
        'symbiosis',
        'rebates',
        'coeds',
        'abdication',
        'ridiculous',
        'damage',
        'motives',
        'david guest',
        'exile',
        'instruments',
        'found',
        'transamerica',
        'donors',
        'presenting',
        'monetary policy',
        'conclusions',
        'inconsistency',
        'truancy',
        'interventions',
        'hot',
        'hardware',
        'verbs',
        'commission',
        'audacity',
        'delegation',
        'redecorating',
        'nice',
        'formalism',
        'leniency',
        'patterns',
        'advantages',
        'disadvantages',
        'legacies',
        'transcripts',
        'reforms',
        'openness',
        'attribution',
        'fiascos',
        'nervous breakdowns',
        'arts',
        'nervous breakdowns',
        'studies',
        'authority',
        'principle',
        'principles',
        'courses',
        'libel',
        'good timing',
        'coordination',
        'class',
        'allowances',
        'hyperactivity',
        'audioclips',
        'live tv',
        'opposition',
        'specialization',
        'libertarianism',
        'prestige',
        'transcription',
        'second opinions',
        'tactlessness',
        'counselling',
        'disbelief',
        'embargos',
        'communications',
        'profitability',
        'clarifications',
        'economic stimulus',
        'overexertion',
        'vested interests',
        'lisps',
        'excess',
        'disloyalty',
        'waivers',
        'insensitivity',
        'isps',
        'reinforcements',
        'postponements',
        'unconsciousness',
        'averages',
        'specimens',
        'registrations',
        'behaviorism',
        'exploitation',
        'deflation',
        'assimilation',
        'service calls',
        'allusions',
        'arrows',
        'taboos',
        'borrowing',
        'foreheads',
        'mutton',
        'elegies',
        'bastardization',
        'responsibilities',        'bluffs',
        'dawn',
        'dusk',
        'grading',
        'collars',
        'rubberboy',
        'hybrids',
        'centerfolds',
        'econometrics',
        'reproduction',
        'rubles',
        'postage',
        'doorbells',
        'building',
        'white',
        'david guest',
        'squatters',
        'stools',
        'nonconformity',
        'long',        'celery',
        'triplets',
        'absorption',
        'osmosis',
        'pepperoni',
        'manslaughter',
        'hangers',
        'dumbwaiters',
        'elites',
        'quito',
        'reindeer',
        'barley',
        'smurfs',
        'beaks',
        'filtration',
        'glands',
        'plasticity',
        'self-discovery',
        'achilles tendon',
        'valedictorians',
        'wonder',
        'plots',
        'chewing',
        'circles',
        'driveways',
        'jam',
        'moma',
        'carbon dioxide',
        'tricycles',
        'sensors',
        'proclamations',
        'encores',
        'espresso',
        'continuous improvement',
        'morse code',
        'antifreeze',
        'mammals',
        'ballot initiatives',
        'referendums',
        'saccharin',
        'governors',
        'auras',
        'lavender',
        'remodeling',
        'itt',
        'last supper',
        'pythagorean theorem',
        'scanners',
        'tata',
        'valets',
        'estrogen',
        'good',
        'assholes',
        'omg',
        'hope',
        'ninjas',
        'masturbating',
        'guys',
        'usa',
        'woods',
        'silly',
        'kids',
        'aspirin',
        'one-liners',
        'fall',
        'sayings',
        'shingles',
        'lucky',
        'unlucky',
        'cops',
        'psyche',
        'shrinking',
        'buoys',
        'weird',
        'cheerios',

    ];

    public function scrrapeTagImage()
    {
        // $tags = Tags::where( 'id', "<", 5001 )->get();
        $tags = Tags::where('id', ">", 5000)->get();
        // $tags = Tags::all();
        // $tags = Tags::limit( 10 )->get();

        // return $tags;

        $tags->map(function ($tag) {
            dispatch(new TagImageProcessing($tag));
        });
    }

    public function scrapeTag()
    {
        // $client = new Client();

        foreach ($this->tags as $tag) {
            dispatch(new TagImageProcessing($tag));

            // $url = 'https://en.wikipedia.org/wiki/' . $tag;
            // $crawler = $client->request('GET', $url);
            // $anchor =  $crawler->filter('div.thumbinner a.image')->first();

            // $authorText = '';
            // $licenseText = '';
            // $descriptionText = '';
            // $shopText = '';

            // if (count($anchor) > 0) {
            //     $href = $anchor->extract(['href'])[0];
            //     $image_page_url = 'https://en.wikipedia.org' . $href;
            //     $image_page = $client->request('GET', $image_page_url);

            //     $full_image_link =  $image_page->filter('.fullImageLink a')->first()->extract(['href'])[0];
            //     $full_image_link = str_replace('//upload', 'upload', $full_image_link);
            //     $full_image_link = 'https://' . $full_image_link;

            //     $description = $image_page->filter('td.description');
            //     $description = ($description->count() > 0) ? $description->first()->text() : "";
            //     $descriptionText = str_replace('English: ', '', $description);

            //     $license = $image_page->filter('table.licensetpl span.licensetpl_short');
            //     $licenseText = ($license->count() > 0) ? $license->first()->text() : "";

            //     $author = $image_page->filter('td#fileinfotpl_aut');
            //     // $author = ($author->count() > 0) ? $author->first()->text() : '';

            //     if ($author->count() > 0) {
            //         $newAuthor = $image_page->filter('td#fileinfotpl_aut')->nextAll();
            //         $newAuthor = $newAuthor->filter('a');
            //         $authorText =  $newAuthor->first()->text();
            //     }

            //     dump($full_image_link);
            //     dump($descriptionText);
            //     dump($licenseText);
            //     dump($authorText);
            // }
        }
    }
}
