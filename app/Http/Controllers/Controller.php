<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var int
     */
    protected $statusCode = FoundationResponse::HTTP_OK;

    /**
     * 获取响应http状态码
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * 设置响应http状态码
     *
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * 封装的底层http响应
     *
     * @param       $data
     * @param array $header
     * @return mixed
     */
    public function respond($data, $header = [])
    {
        return Response::json($data, $this->getStatusCode(), $header);
    }

    /**
     * 封装status
     *
     * @param       $status
     * @param array $data
     * @param null $code
     * @return mixed
     */
    public function status($status, array $data, $code = null)
    {
        if ($code) {
            $this->setStatusCode($code);
        }

        $status = [
            'status' => $status,
            'code' => $this->statusCode
        ];

        if (config('app.debug')) {
            $status['logs'] = DB::getQueryLog();
        }

        $data = array_merge($status, $data);


        return $this->respond($data);
    }

    /**
     * 消息响应
     *
     * @param        $message
     * @param string $status
     * @return mixed
     */
    public function message($message, $status = "success")
    {
        return $this->status($status, [
            'message' => $message
        ]);
    }


    /**
     * 创建成功响应
     *
     * @param string $message
     * @return mixed
     */
    public function created($message = "created")
    {
        return $this->setStatusCode(FoundationResponse::HTTP_CREATED)
            ->message($message);
    }

    /**
     * 通用返回
     *
     * @param $data
     * @return mixed
     */
    public function returnMsg($data)
    {
        if ($data['success'] == true) {
            if ($data['data']) {
                return $this->success($data['data']);
            }
            return $this->message($data['msg']);
        } else {
            return $this->failed($data['msg']);
        }
    }

    /**
     * 成功响应
     *
     * @param        $data
     * @param string $status
     * @return mixed
     */
    public function success($data, $status = "success")
    {
        if ($data['success'] === false) {
            return $this->returnMsg($data);
        }
        return $this->status($status, compact('data'));
    }

    /**
     * 失败响应
     *
     * @param        $message
     * @param int $code
     * @param string $status
     * @return mixed
     */
    public function failed($message = '操作失败', $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 'error')
    {
        return $this->setStatusCode($code)->message($message, $status);
    }


    /**
     * HTTP内部服务器错误
     *
     * @param string $message
     * @return mixed
     */
    public function internalError($message = "Internal Error!")
    {
        return $this->failed($message, FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * 不存在的api
     *
     * @param string $message
     * @return mixed
     */
    public function notFond($message = 'Not Fond!')
    {
        return $this->failed($message, FoundationResponse::HTTP_NOT_FOUND);
    }

    /**
     * 接口未授权
     *
     * @param string $message
     * @return mixed
     */
    public function unAuthorized($message = "Unauthorized!")
    {
        return $this->failed($message, FoundationResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * 没有权限操作指定资源
     *
     * @param string $message
     * @return mixed
     */
    public function forbidden($message = "Forbidden!")
    {
        return $this->failed($message, FoundationResponse::HTTP_FORBIDDEN);
    }
}
