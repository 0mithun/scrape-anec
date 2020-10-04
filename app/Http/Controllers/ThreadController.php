<?php

namespace App\Http\Controllers;

use App\Jobs\InsertAmazonLink;
use App\Jobs\StripSlugTagJob;
use App\Jobs\UpdateTagNames;
use App\Jobs\UpvoteJob;
use App\Thread;
use DB;
use Illuminate\Http\Request;

class ThreadController extends Controller {
    /**
     * All Slug update complete
     */
    public function updateSlug( Request $request ) {

// $threads = Thread::where( 'id', '<', 5000 )
        //     ->get();

        $threads = Thread::where( 'id', '>', 120000 )
            ->where( 'id', '<', 140000 )
            ->get();
        dd( $threads );

        $threads->map( function ( $thread ) {

// $thread->update();

            if ( $thread->whereSlug( $slug = str_slug( $thread->title ) )->exists() ) {
                $slug = "{$slug}-{$thread->id}";
            } else {
                $slug = str_slug( $thread->title );
            }

            $thread->update( ['slug' => $slug] );
        } );

        return 'Update';
    }

    /**
     * All Slug update complete
     */
    public function updateWordCount( Request $request ) {

// $threads = Thread::where( 'id', '<', 5000 )
        //     ->get();

        $threads = Thread::where( 'id', '>', 120000 )
            ->where( 'id', '<', 150000 )
            ->get();
        dd( $threads );

        $threads->map( function ( $thread ) {
            $thread->update( ['word_count' => str_word_count( $thread->body )] );
        } );

        return 'Update';
    }

    /**
     *  get all threads source & location value
     */
    public function getSourceLocation( Request $request ) {
        $data = DB::table( 'engine4_article_fields_values' )->select( '*' )->where( 'field_id', 1 )->get();
        dd( $data );

// $threads = Thread::where( 'id', '<', 5000 )

//     ->get();

// $threads = Thread::where( 'id', '>', 120000 )

//     ->where( 'id', '<', 150000 )

//     ->get();

// dd( $threads );

// $threads->map( function ( $thread ) {

//     $thread->update( ['word_count' => str_word_count( $thread->body )] );

// } );

//location 574 /field_id = 2
        //source 30231 /field_id = 1

        return 'Update';
    }

    /**
     *  get all threads source & location value
     */
    public function getSource( Request $request ) {
        $data = Thread::where( 'source', '!=', '' )->get();
        // $data = Thread::where( 'location', '!=', '' )->get();
        dump( $data );

// return $data;

// $threads = Thread::where( 'id', '<', 5000 )

//     ->get();

// $threads = Thread::where( 'id', '>', 120000 )

//     ->where( 'id', '<', 150000 )

//     ->get();

// dd( $threads );

// $threads->map( function ( $thread ) {

//     $thread->update( ['word_count' => str_word_count( $thread->body )] );

// } );

//location 1642
        //source 9612

        return 'Update';
    }

    /**
     *  get all threads source & location value
     */
    public function getLocation( Request $request ) {
        $data = Thread::where( 'location', '!=', '' )->get();
        // $data = Thread::where( 'location', '!=', '' )->get();
        dump( $data );

// return $data;

// $threads = Thread::where( 'id', '<', 5000 )

//     ->get();

// $threads = Thread::where( 'id', '>', 120000 )

//     ->where( 'id', '<', 150000 )

//     ->get();

// dd( $threads );

// $threads->map( function ( $thread ) {

//     $thread->update( ['word_count' => str_word_count( $thread->body )] );

// } );

//location 1642
        //source 9612

        return 'Update';
    }

    /**
     * Update source
     */

    public function updateSource( Request $request ) {
        // $data = DB::table( 'engine4_article_fields_values' )->select( '*' )->where( 'field_id', 1 )->where( 'item_id', '<', 5000 )->get();
        $data = DB::table( 'engine4_article_fields_values' )
            ->select( '*' )
            ->where( 'field_id', 1 )
            ->where( 'item_id', '<', 150000 )
            ->where( 'item_id', '>', 118999 )
            ->get();
        // dd( $data );
        $data->map( function ( $item ) {
            // $thread->update( ['word_count' => str_word_count( $thread->body )] );
            $thread = Thread::where( 'id', $item->item_id )->first();

            if ( $thread ) {

                if ( $thread->source == '' ) {
                    $thread->source = $item->value;
                    $thread->save();
                }

            }

        } );

        return 'Source Update complete';
    }

    /**
     * Update location
     */

    public function updateLocation( Request $request ) {
        // $data = DB::table( 'engine4_article_fields_values' )->select( '*' )->where( 'field_id', 1 )->where( 'item_id', '<', 5000 )->get();
        $data = DB::table( 'engine4_article_fields_values' )
            ->select( '*' )
            ->where( 'field_id', 2 )

// ->where( 'item_id', '<', 150000 )
        // ->where( 'item_id', '>', 118999 )
            ->get();
        // dd( $data );
        $data->map( function ( $item ) {
            // $thread->update( ['word_count' => str_word_count( $thread->body )] );
            $thread = Thread::where( 'id', $item->item_id )->first();

            if ( $thread ) {

                if ( $thread->location == '' ) {
                    $thread->location = $item->value;
                    $thread->save();
                }

            }

        } );

        return 'Location Update complete';
    }

    /**
     * Update cno
     */

    public function updateCNO( Request $request ) {
        $threads = Thread::where( 'id', '>', '119999' )->where( 'id', '<', 150000 )->update( [
            'CNO' => 'N',
        ] );

        return 'CNO Update complete';
    }

    /**
     * Update created_at
     */

    public function updateCreatedAt( Request $request ) {
        Thread::where( 'id', '>', 0 )->update( [
            'created_at' => now(),
        ] );

        return 'CNO Update complete';
    }

    /**
     * get Tags
     */

    public function updateChannel( Request $request ) {
        $threads = Thread::where( 'tags', '=', NULL )->where( 'id', '>', 19999 )->get();
        // dd( $threads );
        $channels = DB::table( 'channels' )->select( 'name' )->get()->pluck( 'name' )->all();
        $threads->map( function ( $thread ) {
            $thread->channel_id = 1;
            $thread->save();
        } );

// $threads->map( function ( $thread ) use ( $channels ) {

//     $tags = $thread->tags;

//     $tags = explode( ',', $tags );

//     foreach ( $tags as $tag ) {

//         if ( in_array( ucfirst( $tag ), $channels ) && $tag != '' ) {

//             $channel = DB::table( 'channels' )->where( 'name', ucfirst( $tag ) )->first();

//             $thread->channel_id = $channel->id;

//             $thread->save();

//             break;

//         } else {

//             $thread->channel_id = 1;

//             $thread->save();

//         }

//     }
        // } );

        return 'channel Update complete';
    }

    public function addChannelToTags() {
        $channels = DB::table( 'channels' )->select( 'name' )->get()->pluck( 'name' )->all();

        foreach ( $channels as $channel ) {
            $tag = DB::table( 'tags' )->where( 'name', strtolower( $channel ) )->first();

            if ( !$tag ) {
                DB::table( 'tags' )->insert( ['name' => strtolower( $channel )] );
            } else {
                dump( 'not found ' );
            }

        }

    }

    public function attachTags() {
        $threads = Thread::where( 'tags', '!=', '' )->where( 'id', '>', 125000 )->where( 'id', '<', 130000 )->get();
        $threads->map( function ( $thread ) {
            $tags = $thread->tags;
            $tags = rtrim( $tags, ',' );
            $tags = explode( ',', $tags );

            foreach ( $tags as $tag ) {

                if ( $tag != '' ) {
                    $findTag = DB::table( 'tags' )->where( 'name', strtolower( $tag ) )->first();

                    if ( $findTag ) {
                        DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $findTag->id] );
                    } else {
                        //Error Here
                        $newTag = DB::table( 'tags' )->insertGetId( ['name' => strtolower( $tag )] );
                        // dd( $newTag );
                        DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $newTag] );
                    }

                }

            }

        } );
    }

    /**
     * Attach channel to tags
     */

    public function attachChannelTags() {
        // $threads = Thread::where( 'channel_id', '!=', '' )->where( 'id', '<', 5000 )->get();
        $threads = Thread::where( 'channel_id', '!=', '' )->where( 'id', '>', 119999 )->where( 'id', '<', 130000 )->get();
        // dd( $threads );

        $threads->map( function ( $thread ) {
            // DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $findTag->id] );
            $channel = DB::table( 'channels' )->where( 'id', $thread->channel_id )->first();
            $tag = DB::table( 'tags' )->where( 'name', strtolower( $channel->name ) )->first();

            if ( $tag ) {
                DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $tag->id] );
            }

        } );
    }

    /**
     * Remove fuplicatye title
     */

    public function removeDuplcateTitle() {

//hello

// $splitString = preg_split( "/('|:|-)/", $string );

        // return trim( array_shift( $splitString ) );

        $threads = Thread::where( 'id', '<', 5 )->get();
        // $threads = Thread::where( 'id', '>', 119999 )->where( 'id', '<', 130000 )->get();

        $threads->map( function ( $thread ) {
            // DB::table( 'thread_tag' )->insert( ['thread_id' => $thread->id, 'tag_id' => $findTag->id] );

            $splitString = preg_split( "/('|:|-)/", $thread->title );

            $newTitle = trim( array_shift( $splitString ) );
            $thread->title = $newTitle;
            $thread->save();
        } );
    }

    public function updateTagNameList() {
        //update thread

        $threads = Thread::all();

// $threads = Thread::where('id', '<', 100)->get();

        foreach ( $threads as $thread ) {
            // dump($thread);
            dispatch( new UpdateTagNames( $thread ) );
        }

    }

    public function replaceAmazonLink() {
        $threads = Thread::all();

// $threads = Thread::where('id', '<', 100)->get();
        foreach ( $threads as $thread ) {
            dispatch( new InsertAmazonLink( $thread ) );
        }

    }

    public function upvote() {
        // $threads = Thread::where('cno', 'C')->limit(100)->get();
        $threads = Thread::where( 'cno', 'C' )->get();

// return $threads;

// $threads = Thread::where('id', '<', 100)->get();
        foreach ( $threads as $thread ) {
            dispatch( new UpvoteJob( $thread ) );
        }

    }

    public function stripSlug() {
        $threads = Thread::all();

// $threads = Thread::where( 'id', '<', 5 )->get();

        foreach ( $threads as $thread ) {
            dispatch( new StripSlugTagJob( $thread ) );
        }

    }

}
