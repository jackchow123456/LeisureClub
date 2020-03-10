<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Encore\Admin\Form::forget(['map', 'editor']);
app('view')->prependNamespace('admin', resource_path('views/admin'));
Admin::css('https://use.fontawesome.com/releases/v5.6.3/css/all.css');
Admin::css('extensions/bootstrap-table/dist/bootstrap-table.css');
Admin::js('extensions/bootstrap-table/dist/bootstrap-table.min.js');
Admin::js('extensions/bootstrap-table/dist/locale/bootstrap-table-zh-CN.js');
//Admin::js('extension/jstree/jquery.tree.min.js');
//Admin::js('extension/ajaxupload.js');