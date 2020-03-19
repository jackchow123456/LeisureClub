<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Media extends Model
{


    protected $guarded = [];
    protected $casts = [
        'meta' => 'array',
    ];
    protected $appends = [
        'image_uri'
    ];

    public function getImageUriAttribute()
    {
        return url($this->path);
    }


    public function __construct(array $attributes = [])
    {

        $this->setTable(config('store.database.table_prefix') . 'media');
        parent::__construct($attributes);
    }


    const IMAGE = 'image';
    const VIDEO = 'video';
    const AUDIO = 'audio';
}
