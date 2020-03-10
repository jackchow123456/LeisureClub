<?php

namespace JackChow;

use Encore\Admin\Extension;

class Specific extends Extension
{
    public $name = 'specific';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';
}