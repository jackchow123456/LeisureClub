<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = ['id'];
    protected $table = 'store_carts';

    public function scopeChecked($query)
    {
        return $query->where('is_checked', 1);
    }

    /**
     * 获取关联到商品信息
     */
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }

    /**
     * 获取用户的购物车列表
     *
     * @param $user_id
     * @param bool $is_checked
     * @return mixed
     */
    public static function getListByUserId($user_id, $is_checked = false)
    {
        return self::where('user_id', $user_id)->with(['goods' => function ($query) {
            $query->select('id', 'name', 'line_price', 'image', 'price');
        }])->when($is_checked, function ($query) {
            $query->checked();
        })->get()->map(function ($row) {
            $spec_info = $row->goods->skus()->find($row->spec_id);
            if ($spec_info) {
                $row->goods->member_price = $spec_info['price'];
                $row->goods->face_img = $spec_info['image'] ?: $row->goods->face_img;
            }

            return $row;
        });
    }
}
