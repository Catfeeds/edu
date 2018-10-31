<?php
namespace app\index\controller;
use think\Controller;
use \driver\jwt\Jwt;

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$allow_origin = array(
    'http://jwt.com',
    "http://localhost:8080",
    "http://localhost:8081",
    "http://localhost:8082",
    "http://localhost:8083",
    "http://localhost:81"
);
if (in_array($origin, $allow_origin)) {
    header("Access-Control-Allow-Origin: ".$origin);
    // 允许请求资源的方式
    header("Access-Control-Request-Method: OPTIONS, GET, POST, PUT, DELETE");
    // 允许跨域访问是自定义header参数authorization
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
}

class Api extends Controller {
    public function __construct(){
        parent::__construct();
    }

    //index.php/index/api/index/   mod/模型/con/控制器/action/方法/data/处理数据
    public function index($data = '') {
        if(empty($data)) {
            $data = Request()->param();
            if (!$authorization = Request()->header('Authorization')) {
                $authorization = false;
            };
            if (!$data) {
                echo getBack(9999, '请重新登录！');
                exit;
            }
        }
        //服务端模块、控制器、方法
        $module = $data[config('request_param.mod')];
        $controller = $data[config('request_param.con')];
        $action = $data[config('request_param.action')];
        unset($data[config('request_param.mod')]);
        unset($data[config('request_param.con')]);
        unset($data[config('request_param.action')]);
        $curlData['app_id'] = config('app_id');
        $time = time();
        settype($time, "string");
        $curlData['time'] = $time;
        $url = config('host_name') . Url('/' . $module . '/' . $controller . '/' . $action . '');
        $jwt = new Jwt();
        $payload_request = $jwt->verifyToken($authorization);
        $expTime = $time + (10 * 60);//过期时间为10分钟
        $token_response = '';

        $payload_request = [
            'uid' => 'B608AA535E0EC9C5A7BC8548AF39D4CC',
            'iat' => $time,
            'exp' => $expTime,
            'nbf' => $time,
        ];

        if (strtoupper($controller) == "INDEX") {
            if ($payload_request) {
                $data['uid'] = $payload_request['uid'];
                $payload_request['iat'] = $time;
                $payload_request['exp'] = $expTime;
                $payload_request['nbf'] = $time;
                $token_response = $jwt->getToken($payload_request);
            } else {
                $data['uid'] = '';
            }
            /*if (!session('uid')) {
                $data['uid'] = '';
            } else {
                $data['uid'] = session('uid');
            }*/
        } else {
            if ($payload_request) {
                $data['uid'] = $payload_request['uid'];
                $payload_request['iat'] = $time;
                $payload_request['exp'] = $expTime;
                $payload_request['nbf'] = $time;
                $token_response = $jwt->getToken($payload_request);
            } else {
                echo getBack(9999, '请重新登录！');
                exit;
            }
            /*if (!session('uid')) {
                echo getBack(9999, '请重新登录！');
                exit;
            } else {
                $data['uid'] = session('uid');
            }*/
        }
        //缓存文件
        $picInfo = $this->picCache($curlData);
        $picInfo && $data['fileinfo'] = $picInfo['fileinfo'];
        $data['request_ip'] = request()->ip();
        $curlData['data'] = json_encode($data);
        $Common = Model('Common');
        $jsonData = $Common->getInfo($url, $curlData, 'POST', array(), true );
        $picInfo['path'] && $this->delPic($picInfo['path']);//删除缓存文件
        $resultData = json_decode($jsonData, true);
        if (is_array($resultData) && array_key_exists('status', $resultData) && $resultData['status'] == 0) {
            if (!empty($resultData['tab']) && $resultData['tab'] == 'createSession') {
                $time = time();
                $expTime = $time + (10 * 60);//过期时间为10分钟
                $payload_response = [
                    'iat' => $time, // (Issued At)在什么时候签发的(UNIX时间)，是否使用是可选的
                    'exp' => $expTime, // (Expiration Time)什么时候过期，这里是一个Unix时间戳，是否使用是可选的
                    'nbf' => $time, // (Not Before)如果当前时间在nbf里的时间之前，则Token不被接受；一般都会留一些余地，比如几分钟；，是否使用是可选的
                //    'sub' => 'www.admin.com', // (Subject)该JWT所面向的用户，是否使用是可选的
                    'jti' => config('api_key') // (JWT ID)为JWT提供了唯一的标识符,此声明的使用是可选的
                ];
                foreach (config('user_list') as $v) {
                    $payload_response[$v] = $resultData['data'][$v];
                }
                $token_response = $jwt->getToken($payload_response);
                /*$sessionArr = config('user_list');
                foreach ($sessionArr as $v) {
                    if ($v == 'logintime') {
                        session($v, date('Y-m-d H:i:s', $resultData['data'][$v]));
                    } else {
                        session($v, $resultData['data'][$v]);
                    }
                }*/
            } else if (!empty($resultData['tab']) && $resultData['tab'] == 'files') {
                $means = isset($data['means']) ? $data['means'] : false;
                fileManage($resultData['data']['file_path'], $resultData['data']['name'], $resultData['data']['types'], $means = false);
            }
        }
        echo $jsonData;die;
        $resultData['auth_token'] = $token_response;
        $jsonData = json_encode($resultData);
        echo $jsonData;
        exit;
    }
    /*
     * return array[hash, path]
     */
    public function picCache(&$data) {
        $files = request()->file(config('upload_file_name'));
        if ($files) {
            if (is_array($files)) {
                foreach($files as $key => $val) {
                    $info = $val->move(ROOT_PATH . 'public' . DS . 'tempfile');
                    if ($info) {
                        $data['file_' . $key] = curl_file_create($info->getRealPath(), $info->getInfo('type'));
                        $fileinfo['fileinfo']['hash_' . $key] = $info->md5();
                        $fileinfo['fileinfo']['name_' . $key] = $val->getInfo('name');
                        $fileinfo['path'][] = $info->getRealPath();
                    } else {
                        echo getBack(1, $val->getError());
                        exit;
                    }
                }
            } else {
                $info = $files->move(ROOT_PATH . 'public' . DS . 'tempfile');
                if ($info) {
                    $data['file_' . 0] = curl_file_create($info->getRealPath(), $info->getInfo('type'));
                    $fileinfo['fileinfo']['hash_' . 0] = $info->md5();
                    $fileinfo['fileinfo']['name_' . 0] = $files->getInfo('name');
                    $fileinfo['path'][] = $info->getRealPath();
                } else {
                    echo getBack(1, $files->getError());
                    exit;
                }
            }
            return $fileinfo;
        } else {
            return false;
        }
    }
    public function delPic($savepath){
        if (!empty($savepath)) {
            foreach($savepath as $val) {
                if (file_exists($val)) {
                    unlink($val);
                    $dir = dirname($val);
                    $arr = array_diff(scandir($dir), array('..', '.'));
                    if(empty($arr)){
                        rmdir($dir);     //删除空目录
                    }
                }
            }
        }
    }

}
