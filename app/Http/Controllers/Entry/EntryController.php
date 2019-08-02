<?php
/**
 * Created by PhpStorm.
 * User: zhouminjie
 * Date: 2019-07-12
 * Time: 14:30
 */

namespace App\Http\Controllers\Entry;

use App\Exports\EntryExport;
use App\Http\Controllers\Controller;
use App\Imports\EntryImport;
use App\Jobs\ProcessPodcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EntryController extends Controller
{

    public function QiNiuUploadExample(Request $request)
    {
        $this->validate($request, [
            'photo' => 'required|file|max:2048',
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
        return $this->success($result[0]);
    }

    /**
     * excel导出
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exampleExport()
    {
        return Excel::download(new EntryExport(), 'EntryExampleData.xlsx');
    }

    /**
     * excel导入
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function exampleImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|max:2048|mimes:xlsx',
        ]);

        Excel::import(new EntryImport(), request()->file('file'));
    }

    /**
     * 队列任务
     */
    public function exampleJobs()
    {
        ProcessPodcast::dispatch();
    }

    /**
     * 投票
     *
     * @param Request $request
     * @return mixed
     */
    public function vote(Request $request)
    {
        $id = $request->input('id');
        $num = Redis::incr('vote_' . $id);
        $data = json_encode(['id' => $id, 'num' => $num]);
        $data = str_replace("\"", "\\\"" , $data);
        Redis::connection('default')->publish("abc", '["vote","' . $data . '"]');
        return $this->success(['id' => $id, 'num' => $num]);
    }

}