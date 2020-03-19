<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class GoodsAttrValue extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {

        $this->setTable(config('store.database.table_prefix') . 'goods_attr_values');

        parent::__construct($attributes);
    }

    public function attr()
    {
        return $this->belongsTo(GoodsAttrValue::class);
    }
}
