<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\AdImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdController extends Controller
{

    public function getBanner()
    {
        $result = AdImage::query()->with(['ad'=>function($query){
            $query->where('name','首页轮播图');
        }])->orderBy('sort')->get();

        return $this->success($result);
    }
}
