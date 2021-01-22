<?php

namespace App;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class NewThreads extends Model
{
    use SpatialTrait;

    protected $spatialFields = [
        'location',
    ];

    protected $table = 'threads_new';

    protected $guarded = [];
}
