<?php

namespace App\Admin\Controllers\Store;

use App\Models\Store\Ad;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Models\Store\Goods;
use Illuminate\Support\Str;
use App\Repository\Admin\GoodsRepository;

class AdController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'banner管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ad());

        $grid->column('id', __('Id'));
        $grid->column('name', __('名称'));

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
        $form = new Form(new Ad());


        $form->text('name', __('名称'));
        $form->hasMany('banners','banners',function (Form\NestedForm $form){
            $form->image('image');
            $form->number('sort')->min(0);
            $form->textarea('message');
        });

        return $form;
    }


}
