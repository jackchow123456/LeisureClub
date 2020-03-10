<?php

namespace JackChow;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;

class SpecificServerProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Specific $extension)
    {
        if (!Specific::boot()) {
            return;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'specific');
        }

        Admin::booting(function () {
            Form::extend('specific', SpecificField::class);
        });
    }
}