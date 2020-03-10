<?php

namespace App\Admin\Controllers;

use App\Models\Posts;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '帖子管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Posts());

        $grid->quickSearch('title');

        $grid->filter(function ($filter) {
            // 在这里添加字段过滤器
            $filter->like('title', 'title');
            $filter->like('title', 'title');

        });

        $grid->column('id', __('ID'))->sortable();
        $grid->column('title', __('标题'))->ucfirst()->limit(30);
        $grid->user('作者')->pluck('name')->label();
        $grid->column('published_date', __('发布时间'));
        $states = [
            'on' => ['text' => '是'],
            'off' => ['text' => '否'],
        ];
        $grid->status('是否显示')->switch($states);

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
        $form = new Form(new Posts());

        $form->display('id', __('ID'));
        $form->text('title', __('标题'));
        $form->select('user_id', __('作者'))->options(function ($id) {
            $user = User::find($id);
            if ($user) {
                return [$user->id => $user->name];
            }
        })->ajax('/admin/api/users');

        $form->date('published_date', __('发布时间'))->format('YYYY-MM-DD')->style('width','150px');
        $states = [
            'on' => ['value' => 1, 'text' => '是', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
        ];

        $form->switch('status', __('是否显示'))->states($states)->default(1);
        $form->UEditor('content', __('内容'));
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        return $form;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function users(Request $request)
    {
        $q = $request->get('q');

        return User::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    public function upload_file(Request $request)
    {
        // key为file
        if($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('images',['disk'=>'public']);

            // 返回格式
            return ['url'=> Storage::url($path)];
        }
    }
}
