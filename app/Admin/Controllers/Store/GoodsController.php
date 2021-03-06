<?php

namespace App\Admin\Controllers\Store;

use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Models\Store\Goods;
use Illuminate\Support\Str;
use App\Repository\Admin\GoodsRepository;

class GoodsController extends AdminController
{
    public $savePathPrefix = 'upload/admin/';


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
            $form->textarea('short_description', __('分享描述'))->value('12312312312')->rows(3)->required();
            $form->select('user_id', __('设置管理员'))->options(function ($id) {
                $user = User::find($id);
                if ($user) {
                    return [$user->id => $user->name];
                }
            })->ajax('/admin/api/users')->value(1)->required();
            $form->text('store_id', __('店铺id'))->value(0)->required();
            $form->multipleImage('me', __('商品画册'))->removable()->help('可上传多个图片')->options([
                'maxFileCount' => 5     //最大上传文件数为5
            ]);
            $form->image('image', __('商品图'))->removable();
            $form->dateRange('start_date', 'end_date', __('上架时间'));
//            $form->divider('价格库存');
//            $form->sku('sku', '商品规格');
            $form->currency('price','价格')->symbol('￥');
            $form->number('stock_num','库存');
            $form->specific('sku', '商品规格');

            $form->UEditor('content', __('内容'));


        });

        !$form->isEditing() && $form->ignore(['me', 'sku']);

        $form->saved(function (Form $form) {
            $data = \Request::all();

            $goodsRepository = new GoodsRepository();
            isset($data['me']) && $goodsRepository->handleMeAttribute($form->builder()->field('me')->prepare($data['me']), $form->model());
            isset($data['sku']) && $goodsRepository->handleSku(json_decode(data_get($data, 'sku','{}'), true), $form->model());

        });

        return $form;
    }


}
