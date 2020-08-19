<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Geocoder\Geocoder;

class SetLocationJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $thread;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Thread $thread ) {
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // dump( $this->thread );
        $location = $this->getGeocodeing( $this->thread->location );
        $data = [];
        if ( $location['accuracy'] != 'result_not_found' ) {
            $data['lat'] = $location['lat'];
            $data['lng'] = $location['lng'];
            $this->thread->update( $data );

        }

        dump( 'location update successfully' );
    }

    public function getGeocodeing( $address ) {
        $client = new \GuzzleHttp\Client();

        $geocoder = new Geocoder( $client );

        $geocoder->setApiKey( config( 'geocoder.key' ) );

        $geocoder->setCountry( config( 'geocoder.country', 'US' ) );

        return $geocoder->getCoordinatesForAddress( $address );
    }

}
