<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{

    public $path = 'upload';
    public $basicPath;

    public function __construct()
    {
        $this->basicPath = public_path($this->path);
    }

    public function index(Request $request)
    {
        $field = $request->get('field');
        return view('admin.file_manager.main', compact('field'));
    }

    public function listFolders()
    {
        $json = array();
        $directories = glob(rtrim($this->basicPath, '/') . '/*', GLOB_ONLYDIR);


        if ($directories) {
            $i = 0;
            foreach ($directories as $directory) {
                $json[$i]['data'] = basename($directory);
                $json[$i]['attributes']['directory'] = utf8_substr($directory, strlen($this->basicPath));

                $children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);

                if ($children) {
                    $json[$i]['children'] = $this->readDir($directory);
                } else {
                    $json[$i]['children'] = '';
                }
                $i++;
            }
        }
        return $json;
    }

    public function files(Request $request)
    {
        $json = array();

        if (!empty($request->get('directory'))) {
            $directory = $this->basicPath . str_replace('../', '', $request->get('directory'));
        } else {
            $directory = $this->basicPath;
        }

        $allowed = array(
            '.jpg',
            '.jpeg',
            '.png',
            '.gif'
        );

        $files = glob(rtrim($directory, '/') . '/*');

        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    $ext = strrchr($file, '.');
                } else {
                    $ext = '';
                }

                if (in_array(strtolower($ext), $allowed)) {
                    $size = filesize($file);

                    $i = 0;

                    $suffix = array(
                        'B',
                        'KB',
                        'MB',
                        'GB',
                        'TB',
                        'PB',
                        'EB',
                        'ZB',
                        'YB'
                    );

                    while (($size / 1024) > 1) {
                        $size = $size / 1024;
                        $i++;
                    }


                    $json[] = array(
                        'filename' => basename($file),
                        'file' => $this->path . utf8_substr($file, utf8_strlen($this->basicPath)),
                        'fileuri' => url($this->path . utf8_substr($file, utf8_strlen($this->basicPath))),
                        'size' => round(utf8_substr($size, 0, utf8_strpos($size, '.') + 4), 2) . $suffix[$i],
                        'filetime' => filectime($file),
                        'filesize' => filesize($file),
                        'updTime' => date('Y-m-d H:i', filectime($file)),
                    );
                }
            }
        }

        return $json;
    }


    protected function readDir($path)
    {
        $directories = glob(rtrim($path, '/') . '/*', GLOB_ONLYDIR);
        $return = array();
        if ($directories) {
            $i = 0;
            foreach ($directories as $directory) {
                $return[$i]['data'] = basename($directory);
                $return[$i]['attributes']['directory'] = utf8_substr($directory, strlen($this->basicPath));

                $children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);

                if ($children) {
                    $return[$i]['children'] = $this->readDir($directory);
                } else {
                    $return[$i]['children'] = '';
                }
                $i++;
            }
        }
        return $return;
    }

    public function image(Request $request)
    {
        if ($request->get('image')) {
            if (file_exists(public_path() . $request->get('image'))) {
                return $request->get('image');
            }
        }
        return asset('admin_template/add-img.png');
    }

    public function create(Request $request)
    {
        $json = array();

        if (isset($request['directory'])) {
            if (isset($request['name']) || $request['name']) {
                $directory = rtrim($this->basicPath . str_replace('../', '', $request['directory']), '/');

                if (!is_dir($directory)) {
                    $json['error'] = '警告：请选择一个目录!';
                }

                if (file_exists($directory . '/' . str_replace('../', '', $request['name']))) {
                    $json['error'] = '警告：文件名或目录名已存在!';
                }

//                if (!preg_match('/^\w+$/i', $request['name'])) {
//                    $json['error'] = '警告：文件名非法!';
//                }

            } else {
                $json['error'] = '警告：请输入一个新名称!';
            }
        } else {
            $json['error'] = '警告：请选择一个目录!';
        }

        if (!isset($json['error'])) {
            mkdir($directory . '/' . str_replace('../', '', $request['name']), 0775);

            $json['success'] = '成功：创建目录!';
        }

        return $json;
    }

    public function directory(Request $request)
    {
        $json = array();
        if (isset($request['directory'])) {
            $directories = glob(rtrim($this->basicPath . str_replace('../', '', $request['directory']), '/') . '/*', GLOB_ONLYDIR);

            if ($directories) {
                $i = 0;

                foreach ($directories as $directory) {
                    $json[$i]['data'] = basename($directory);
                    $json[$i]['attributes']['directory'] = utf8_substr($directory, strlen($this->basicPath));

                    $children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);

                    if ($children) {
                        $json[$i]['children'] = ' ';
                    }

                    $i++;
                }
            }
        }
        return $json;
    }


    /**
     * 删除
     *
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        $json = array();

        $is_file = false;

        if (isset($request['path'])) {

            $is_file = is_array($request['path']) ? true : false;

            if ($is_file) {
                $path = [];
                foreach ($request['path'] as $v) {

                    $filePath = rtrim($this->basicPath . str_replace($this->path, '', $v));

                    if (!file_exists($filePath)) {
                        $json['error'] = '警告：请选择一个目录或文件!';
                    }

                    if ($filePath == rtrim($this->basicPath, '/')) {
                        $json['error'] = '警告：您不能删除此目录!';
                    }

                    $path[] = $filePath;
                }
            } else {
                $path = rtrim($this->basicPath . str_replace($this->path, '', $request['path']));

                if (!file_exists($path)) {
                    $json['error'] = '警告：请选择一个目录或文件!';
                }

                if ($path == rtrim($this->basicPath, '/')) {
                    $json['error'] = '警告：您不能删除此目录!';
                }
            }
        } else {
            $json['error'] = '警告：请选择一个目录或文件!';
        }

        if (!isset($json['error'])) {
            if ($is_file) {
                foreach ($path as $v) {
                    unlink($v);
                }
            } elseif (is_dir($path)) {
                $files = array();

                $originPath = $path;

                $path = array($path . '/*');

                while (count($path) != 0) {
                    $next = array_shift($path);
                    foreach (glob($next) as $file) {
                        if (is_dir($file)) {
                            $path[] = $file . '/*';
                        }
                        $files[] = $file;
                    }

                }
                rsort($files);
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    } elseif (is_dir($file)) {
                        rmdir($file);
                    }
                }

                rmdir($originPath);
            }


            $json['success'] = '成功：文件或目录已被删除!';
        }

        return $json;
    }

    public function folders()
    {
        return $this->recursiveFolders($this->basicPath);
    }

    protected function recursiveFolders($directory)
    {
        $output = '';

        $output .= '<option value="' . utf8_substr($directory, strlen($this->basicPath)) . '">' . utf8_substr($directory, strlen($this->basicPath)) . '</option>';

        $directories = glob(rtrim(str_replace('../', '', $directory), '/') . '/*', GLOB_ONLYDIR);

        foreach ($directories as $directory) {
            $output .= $this->recursiveFolders($directory);
        }

        return $output;
    }

    /**
     * 移动
     *
     * @param Request $request
     * @return array
     */
    public function move(Request $request)
    {

        $json = array();

        if (isset($request['from']) && isset($request['to'])) {

            $is_file = is_array($request['from']) ? true : false;

            $to = rtrim($this->basicPath . str_replace($this->path, '', $request['to']));

            if (!file_exists($to)) {
                $json['error'] = '警告：移动至目标目录不存在!';
            }

            if ($is_file) {
                $from = [];

                foreach ($request['from'] as $v) {

                    $filePath = rtrim($this->basicPath . str_replace($this->path, '', $v));

                    if (!file_exists($filePath)) {
                        $json['error'] = '警告：请选择一个目录或文件!';
                    }

                    if ($filePath == rtrim($this->basicPath, '/')) {
                        $json['error'] = '警告：您不能删除此目录!';
                    }

                    if (file_exists($to . '/' . basename($filePath))) {
                        $json['error'] = '警告：文件名或目录名已存在!';
                    }

                    $from[] = $filePath;
                }
            } else {
                $from = rtrim($this->basicPath . str_replace($this->path, '', $request['from']));
                $from = rtrim($from, '/');

                if (!file_exists($from)) {
                    $json['error'] = '警告：文件或目录不存在!';
                }

                if ($from == $this->basicPath) {
                    $json['error'] = '警告：不能更改默认目录!';
                }

                if (file_exists($to . '/' . basename($from))) {
                    $json['error'] = '警告：文件名或目录名已存在!';
                }
            }


        } else {
            $json['error'] = '警告：请选择一个目录!';
        }

        if (!isset($json['error'])) {
            if (is_array($from)) {
                foreach ($from as $v) {
                    rename($v, $to . '/' . basename($v));
                }
            } else {
                rename($from, $to . '/' . basename($from));
            }

            $json['success'] = '成功：文件或目录已被移动!';
        }

        return $json;
    }

    /**
     * 复制
     *
     * @param Request $request
     * @return array
     */
    public function copy(Request $request)
    {

        $json = array();

        if (isset($request['path']) && isset($request['name'])) {
            if ((utf8_strlen($request['name']) < 1) || (utf8_strlen($request['name']) > 255)) {
                $json['error'] = '警告：文件名必须在3至255个字符之间!';
            }

            $old_name = rtrim($this->basicPath . str_replace($this->path, '', $request['path']), '/');

            if (!file_exists($old_name) || $old_name == $this->basicPath) {
                $json['error'] = '警告：无法复制这个文件或目录!';
            }

            if (is_file($old_name)) {
                $ext = strrchr($old_name, '.');
            } else {
                $ext = '';
            }

            $new_name = dirname($old_name) . '/' . str_replace($this->path, '', $request['name'] . $ext);

            if (file_exists($new_name)) {
                $json['error'] = '警告：文件名或目录名已存在!';
            }
        } else {
            $json['error'] = '警告：请选择一个目录或文件!';
        }


        if (!isset($json['error'])) {
            if (is_file($old_name)) {
                copy($old_name, $new_name);
            } else {
                $this->recursiveCopy($old_name, $new_name);
            }

            $json['success'] = '成功：文件或目录已被复制!';
        }

        return $json;
    }

    function recursiveCopy($source, $destination)
    {
        $directory = opendir($source);

        @mkdir($destination);

        while (false !== ($file = readdir($directory))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($source . '/' . $file)) {
                    $this->recursiveCopy($source . '/' . $file, $destination . '/' . $file);
                } else {
                    copy($source . '/' . $file, $destination . '/' . $file);
                }
            }
        }

        closedir($directory);
    }

    /**
     * 重命名
     *
     * @param Request $request
     * @return array
     */
    public function rename(Request $request)
    {
        $json = array();

        if (isset($request['path']) && isset($request['name'])) {
            if ((utf8_strlen($request['name']) < 1) || (utf8_strlen($request['name']) > 255)) {
                $json['error'] = '警告：文件名必须在3至255个字符之间!';
            }

//            if (!preg_match('/^\w+$/i', $request['name'])) {
//                $json['error'] = '名字非法';
//            }

            $old_name = rtrim($this->basicPath . str_replace($this->path, '', $request['path']), '/');

            if (!file_exists($old_name) || $old_name == $this->basicPath) {
                $json['error'] = '警告：不能重命名此目录!';
            }

            if (is_file($old_name)) {
                $ext = strrchr($old_name, '.');
            } else {
                $ext = '';
            }

            $new_name = dirname($old_name) . '/' . str_replace(' ', '_', str_replace('../', '', $request['name']) . $ext);

            if (file_exists($new_name)) {
                $json['error'] = '警告：文件名或目录名已存在!';
            }
        }


        if (!isset($json['error'])) {
            rename($old_name, $new_name);

            $json['success'] = '成功：文件或目录已被重命名!';
        }

        return $json;
    }

    /**
     * 文件上传
     *
     * @param Request $request
     * @return array
     */
    public function upload(Request $request)
    {
        $json = array();
        $directory = $request['directory'] . '/';

        $file_name = $request->file()['image']->getClientOriginalName();
        $savePath = $this->path . $directory . $file_name;
        if (file_exists($savePath)) {
            $json['error'] = '警告：文件名或目录名已存在!';
            $json['path'] = $savePath;
            return $json;
        }
        try {
            $request->file()['image']->storeAs($this->path . $directory, $file_name);
        } catch (\Exception $e) {
            $json['error'] = '警告：上传文件非法!';
            $json['path'] = $savePath;
            return $json;
        }

        $imgInfo = getimagesize($request->file()['image']->path());
        list($origWidth, $origHeight) = $imgInfo;
        $json['width'] = $origWidth;
        $json['height'] = $origHeight;
        $json['mime'] = $imgInfo['mime'];
        $json['success'] = '成功：您的文件已上传!';
        $json['status'] = 1;
        return $json;
    }

    public function multiUpload(Request $request)
    {
        $directory = $request->get('directory');
        return view('admin/file_manager/multiUpload', compact('directory'));
    }

}
