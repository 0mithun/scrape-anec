<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    protected $guarded = [];
    protected $table = 'tags';



    public function threads()
    {
        //        return $this->belongsToMany(Thread::class);
        return $this->belongsToMany(Thread::class, 'thread_tag', 'tag_id', 'thread_id');
    }
}
