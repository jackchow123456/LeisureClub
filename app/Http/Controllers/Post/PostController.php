<?php

namespace App\Http\Controllers\Post;

use App\Models\Posts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $list = Posts::all();

        if (!empty($list)) {
            foreach ($list as $key => $item) {
                $list[$key]['user'] = $item->user;
            }
        }

        return $this->success($list);
    }
}
