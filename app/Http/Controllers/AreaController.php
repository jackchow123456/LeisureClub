<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function getArea(Request $request)
    {
        $preId = $request->input('pre_id', 1);
        $data = DB::connection()->table('area')
            ->select('area_id', 'pre_id', 'name')
            ->where('pre_id', $preId)
            ->get()->map(function ($row) use ($preId) {
                if ($preId == 1) {
                    $row->hasSub = true;
                    return $row;
                }
                $row->hasSub = DB::connection()->table('area')->select('area_id')->where('pre_id', $row->area_id)->first() ? true : false;
                return $row;
            });
        return $this->success($data);
    }
}
