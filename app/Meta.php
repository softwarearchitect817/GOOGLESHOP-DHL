<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Meta extends Model
{
    use HasTranslations;
    public $timestamps = false;
    public $translatable = ['value'];
}