<?php
/**
 * Created by PhpStorm.
 * User: zhouminjie
 * Date: 2019-07-12
 * Time: 14:30
 */

namespace App\Http\Controllers\Entry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Illuminate\Support\Str;

class EntryController extends Controller
{

    public function QiNiuUploadExample(Request $request)
    {
        $this->validate($request, [
            'photo' => 'required|file|max:2048|mimes:jpeg,png',
        ]);

        $photo = $request->file('photo')->get();

        $accessKey = 'QnQJTaXfzlxsluPjRA4mgC4pla9cJKp5mteA6BBJ';
        $secretKey = 'B1Lan4BzU_TwcjgOjvYMX-7bRP7IvnWsac4TSPx_';
        $base_url = 'http://puirimanu.bkt.clouddn.com/';
        $auth = new Auth($accessKey, $secretKey);
        $bucket = 'jackchow';

        // 上传图片
        $token = $auth->uploadToken($bucket);
        $uploadMgr = new UploadManager();
        $result = $uploadMgr->put($token, Str::random(40), $photo);
        if (!$result[0]) {
            return $this->failed('上传图片失败.');
        }

        $result[0]['base_url'] = $base_url;
        return $this->success($result);
    }
}