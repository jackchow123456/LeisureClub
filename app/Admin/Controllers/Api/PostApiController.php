<?php

namespace App\Admin\Controllers\Api;

use App\Models\Posts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostApiController extends Controller
{
    public function getList(Request $request)
    {
        $pageSize = $request->input('pageSize', 15);
//        $a = DB::connection()->table('')->paginate();
        $condition = [];

        $key = $request->input('key');
        $value = $request->input('value');
        $value && $condition[$key] = $value;
        $list = Posts::select('id', 'title', 'user_id', 'created_at')->when(!empty($condition),function ($j) use ($condition){
            $j->where($condition);
        })->paginate($pageSize);
        return ['total' => $list->total(), 'totalNotFiltered' => $list->total(), 'rows' => $list->items()];
    }
}
