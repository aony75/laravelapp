<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Webpatser\Uuid\Uuid;

class People extends Model
{
    //id is the primary in the people table
    protected $primaryKey = 'id'; 

    /**
     *  Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();

        //generating UUID automatically
        self::creating(function ($model) {
            $model->UUID = substr((string) Uuid::generate(4), 0, 8);
        });
    }
}

