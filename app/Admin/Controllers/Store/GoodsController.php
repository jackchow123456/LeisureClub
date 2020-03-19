<?php

namespace App\Admin\Controllers\Store;

use App\Models\Store\GoodsAttr;
use App\Models\Store\GoodsAttrValue;
use App\Models\Store\GoodsSku;
use App\Models\Store\Media;
use App\Models\Store\MediaCategory;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Store\Goods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoodsController extends AdminController
{

    public $store_id = 0;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Goods);

        $grid->column('id', __('Id'));
        $grid->column('name', __('商品名称'));
        $grid->column('description', __('商品描述'));
        $grid->column('price', __('价格'));
        $grid->column('line_price', __('划线价'));
        $grid->column('stock_num', __('库存'));

        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('修改时间'));

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Goods());

        $form->tab('基本信息', function ($form) {

            $form->divider('基本信息');

            $form->text('name', __('商品名称'))->value(Str::random(10))->required();
            $form->textarea('description', __('分享描述'))->value('12312312312')->rows(3)->required();
            $form->select('user_id', __('设置管理员'))->options(function ($id) {
                $user = User::find($id);
                if ($user) {
                    return [$user->id => $user->name];
                }
            })->ajax('/admin/api/users')->value(1)->required();
            $form->text('store_id', __('店铺id'))->value(0)->required();
            $form->multipleImage('medias', __('商品画册、'))->removable()->sortable()->help('可上传多个图片')->options([
                'browseClass'=>'btn btn-primary btn-block',
                'showCaption'=>false,
                'showRemove'=>false,
                'showUpload'=>false,
                'layoutTemplates'=>'main1',
                'maxFileCount' => 5     //最大上传文件数为5
            ]);

//            $form->image('images', __('商品图'))->removable()->sortable();

//            $form->divider('价格库存');

//            $form->sku('sku', '商品规格');
            $form->specific('sku', '商品规格');
        });

        if (!$form->isEditing()) {
            $form->ignore('sku');

        }


        $form->saved(function (Form $form) {
            DB::connection()->beginTransaction();

            // sku处理
            $data = \Request::all();
            dd($data);
            $sku = json_decode($data['sku'], true);
            $this->handleSku($sku, $form->model());

            DB::connection()->commit();

        });


        return $form;
    }

    /**
     * 处理上传的sku信息
     *
     * @param $sku
     * @param $model
     */
    protected function handleSku($sku, $model)
    {
        if (empty($sku)) return;

        $attrs = $sku['attrs'];
        $this->handleSkuAttr($attrs);
        GoodsSku::where('goods_id', $model->id)->delete();

        $ignoreKey = ['price', 'stock'];
        $skus = $sku['sku'];

        foreach ($skus as $sk => $item) {
            $key = $keyName = [];
            $media = false;
            foreach ($item as $k => $vitem) {
                if (in_array($k, $ignoreKey)) continue;

                if (is_array($vitem) && $k == 'pic') {
                    $media = $this->handleSkuMedia($vitem);
                    continue;
                }

                $value = GoodsAttrValue::with(['attr' => function ($j) use ($k) {
                    $j->where('name', $k);
                }])->where('name', $vitem)->first();

                $key[] = $value->getKey();
                $keyName[] = $k . ':' . $vitem;
            }

            $key_name = implode(',', $keyName);
            $media_id = $media ? $media->getKey() : 0;

            Log::debug('meida_id:',[$media_id]);
            Log::debug('price:',[$item['price']]);

            $goodsSku = GoodsSku::create([
                'goods_id' => $model->id,
                'key' => implode('_', $key),
                'key_name' => $key_name,
                'media_id' => $media_id,
                'price' => $item['price'],
                'stock' => $item['stock'],
            ]);


            if ($media_id > 0) MediaCategory::where('id', $media_id)->update(['use_id' => $goodsSku->getKey(), 'name' => $model->name . "【{$key_name}】图片"]);
        }

    }

    /**
     * 处理上传规格的属性和属性值
     *
     * @param $attrs
     */
    protected function handleSkuAttr($attrs)
    {
        if (!$attrs) return;

        foreach ($attrs as $attr => $attrValues) {
            $goods_attr = GoodsAttr::updateOrCreate([
                'store_id' => $this->store_id,
                'name' => $attr,
            ], [
                'sort' => 0
            ]);

            foreach ($attrValues as $attrValue) {
                GoodsAttrValue::updateOrCreate([
                    'goods_attr_id' => $goods_attr->getKey(),
                    'store_id' => $this->store_id,
                    'name' => $attrValue,
                ], [
                    'sort' => 0
                ]);
            }
        }
    }

    /**
     * 处理规格图片
     *
     * @param $skuImages
     * @return |null
     */
    protected function handleSkuMedia($skuImages)
    {
        if (!$skuImages) return null;

        $mediaCategory = MediaCategory::create([
            'name' => '',
            'store_id' => $this->store_id,
            'use' => '商品规格',
            'type' => 'image',
        ]);

        foreach ($skuImages as $item) {
            Media::create([
                'store_id' => $this->store_id,
                'mc_id' => $mediaCategory->getKey(),
                'type' => 'image',
                'path' => $item,
            ]);
        }

        return $mediaCategory;
    }


}
