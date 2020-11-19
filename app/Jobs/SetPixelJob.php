<?php

namespace App\Jobs;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetPixelJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    protected $thread;

    /**
     * @var int
     */
    public $timeout = 300;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * The maximum number of exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

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

//amazon_image_path

//amazon_image_path_pixel_color

// $this->setAmazon();
        // $this->setOther();
        $this->setWiki();
    }

    /**
     * Set wiki pixel
     */

    public function setWiki() {
        $pixelColor = $this->getImageColorAttribute( $this->thread->wiki_image_path );

        if ( $pixelColor ) {
            $this->thread->wiki_image_path_pixel_color = $pixelColor;
            $this->thread->save();
        }

    }

    /**
     * Set Amazon pixel
     */

    public function setOther() {
        $pixelColor = $this->getImageColorAttribute( $this->thread->other_image_path );

        if ( $pixelColor ) {
            $this->thread->other_image_path_pixel_color = $pixelColor;
            $this->thread->save();
        }

    }

    /**
     * Set Amazon pixel
     */

    public function setAmazon() {
        $pixelColor = $this->getImageColorAttribute( $this->thread->amazon_image_path );

        if ( $pixelColor ) {
            $this->thread->amazon_image_path_pixel_color = $pixelColor;
            $this->thread->save();
        }

    }

    /**
     * Get rgb color value from image
     */

    public function getImageColorAttribute( $image_path ) {

        if ( $image_path != '' ) {
            $splitName = explode( '.', $image_path );
            $extension = strtolower( array_pop( $splitName ) );

            if ( $extension == 'jpg' ) {
                $im = imagecreatefromjpeg( $image_path );
            }

            if ( $extension == 'jpeg' ) {
                $im = imagecreatefromjpeg( $image_path );
            } else
            if ( $extension == 'png' ) {
                $im = imagecreatefrompng( $image_path );
            } else
            if ( $extension == 'gif' ) {
                $im = imagecreatefromgif( $image_path );
            }

            if ( isset( $im ) ) {

                $rgb = imagecolorat( $im, 0, 0 );
                $colors = imagecolorsforindex( $im, $rgb );
                array_pop( $colors );
                array_push( $colors, 1 );
                $rgbaString = join( ', ', $colors );

                return $rgbaString;
            }

        }

        return false;
    }

}
