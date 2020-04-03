<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class AdImage extends Model
{
    protected $table = 'store_ad_image';
    protected $guarded = [];
    protected $appends = ['image_uri'];

    /**
     * 处理商品图片（设置）
     * @param $key
     */
    public function setImageAttribute($key)
    {
        $key ? $this->attributes['image'] = getSavePath($key) : $this->attributes['image'] = '';
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class,'ad_id','id');
    }

    public function getImageUriAttribute()
    {
        return $this->attributes['image'] ? url($this->attributes['image']) : '';
    }
}
