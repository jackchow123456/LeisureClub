<?php

namespace App\Admin\Controllers\Store;

use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Store\Goods;
use Illuminate\Http\Request;

class GoodsController extends AdminController
{
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
        $form = new Form(new Goods);
        $form->tab('基本信息', function ($form) {

            $form->divider('基本信息');

            $form->text('name', __('商品名称'))->required();
            $form->textarea('description', __('分享描述'))->rows(3);
            $form->multipleImage('gallery', __('商品图'))->removable()->sortable()->help('可上传多个图片')->options([
//                'browseClass'=>'btn btn-primary btn-block',
//                'showCaption'=>false,
//                'showRemove'=>false,
//                'showUpload'=>false,
//                'layoutTemplates'=>'main1',

                'maxFileCount' => 5     //最大上传文件数为5
            ]);

            $form->divider('价格库存');

            $form->sku('sku', '商品规格1');
            $form->specific('sku1', '商品规格');

        });


        return $form;
    }

    public function getSpecific(Request $request)
    {

    }
}
