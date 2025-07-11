<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapedArticles extends Model
{
    protected $fillable = [
        'url',
        'source',
        'checked',
    ];
}
