<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class MediaCategory extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {

        $this->setTable(config('store.database.table_prefix') . 'media_category');

        parent::__construct($attributes);
    }


    /**
     * 分类下的媒体
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function medias()
    {
        return $this->hasMany(Media::class, 'mc_id');
    }

    public static function make(...$parameters)
    {
        return new static(...$parameters);
    }


}
