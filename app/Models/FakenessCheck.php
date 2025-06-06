<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakenessCheck extends Model
{
    protected $fillable = [
        'url',
        'score',
        'title',
        'image',
        'explanation',
        'slug',
        'logo',
        'name',
        'order_num',

    ];
}
