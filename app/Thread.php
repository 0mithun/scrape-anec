<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model {
    protected $guarded = [];

    /**
     * Set the proper slug attribute.
     *
     * @param string $value
     */
    public function setSlugAttribute( $value ) {
        if ( static::whereSlug( $slug = str_slug( $value ) )->exists() ) {
            $slug = "{$slug}-{$this->id}";
        }

        $this->attributes['slug'] = $slug;
    }
}
