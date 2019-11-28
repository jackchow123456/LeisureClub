<?php

namespace App\Admin\Controllers\Store;

use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;

/**
 * 商城首页
 * Class StoreController
 * @package App\Admin\Controllers\Store
 */
class StoreController extends AdminController
{
    public function index(Content $content)
    {
        return $content->header('商城')->body(view('store.index'));
    }
}
