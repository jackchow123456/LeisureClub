<?php

namespace JackChow;

use Encore\Admin\Form\Field;

class SpecificField extends Field
{
    protected $view = 'specific::specific_field';

    protected static $js = [
        'vendor/jackchow/specific/specific.js',
        'vendor/jackchow/layer/layer.js',
        'vendor/laravel-admin/AdminLTE/plugins/select2/i18n/zh-CN.js',
    ];

    protected static $css = [
        'vendor/jackchow/specific/specific.css'
    ];

    public function render()
    {
        $this->script = <<< EOF
window.DemoSpecific = new JackChowSpecific('{$this->getElementClassSelector()}')
EOF;
        return parent::render();
    }
}