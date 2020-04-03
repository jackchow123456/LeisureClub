<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $table = 'store_ad';
    protected $guarded = [];

    public function banners()
    {
        return $this->hasMany(AdImage::class,'ad_id');
    }
}
