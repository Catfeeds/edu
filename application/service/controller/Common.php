<?php
namespace app\service\controller;
use think\Controller;
use app\service\model\Apps;
use \driver\jwt\Jwt;

class Common extends Controller {
    public $error_message = array('status' => '0', 'data' => array());//0：当前验证无误
    public $data = array();
    public $app_key = '';
    public function __construct(){
        parent::__construct();
        //请求认证
        $mustKey = ['app_id', 'time', 'auth'];
        config('app_trace', false);//关闭页面追踪信息，否则页面将不能进行json解析
        //请求参数认证
        $request_data = request()->param();
        $request_data_json = json_decode($request_data['data'], true);
        foreach($mustKey as $v) {
            if (!array_key_exists($v, $request_data) || !array_key_exists('uid', $request_data_json)) {
                $this->error_message['status'] = '1';
                $this->error_message['data'] = '丢失必要信息，请重试！';
                echo getBack(1, '丢失必要信息，请重试！');
                exit;
            }
        }
        //请求认证
        if ($this->error_message['status'] == '0') {
            $this->check($request_data);
            //请求权限认证
            //无需认证模块
            $controller['no'] = explode(',',strtoupper(config('not_auth_module')));
            $request_con = strtoupper(request()->controller());
            if (empty($controller['no']) || (!empty($controller['no']) && !in_array($request_con, $controller['no']))) {
         //       $this->authentication();
            }
        }

        //请求认证
        /*$this->check($request_data);
        //权限认证
        $Rbac = new \org\util\Rbac();
        if ($Rbac::checkAccess()) {
            //判定是否登录
            if ($request_data_json['uid'] === '') {
                echo getBack(1, '该用户未登录！');
                exit;
            }
            //保存用户权限
            //$Rbac::saveAccessList($request_data_json['uid']);
            //判定用户权限
            if (!$Rbac::AccessDecision($request_data_json['uid'])) {
                session(null);
                echo getBack(1, '该用户无此操作权限！');
                exit;
            }
        }*/
    }

    public function check($data) {
        $this->checkTime($data['time']);
        $this->checkAuth($data);
        if ($this->error_message['status'] == 0) {
            $this->data = json_decode($data['data'], true);
            $this->dealwithFile($this->data);
        //    request()->file('file') && $this->data['file'] = request()->file('file');
        } else {
            echo getBack(1, $this->error_message['data']);
            exit;
        }
    }

    protected function checkAuth($data) {
        $auth = $data['auth'];
        unset($data['auth']);
        $text = '';
        ksort($data);
        foreach($data as $key => $val) {
            if ($key === 'app_id' || $key ==='time' || $key === 'data' ) {
                $text .= $val;
            }
        }
        $text = json_encode($text);
        $app_key = $this->getKey($data['app_id']);
        $new_auth = createAuth($text, $app_key);
        if ($new_auth != $auth) {
            $this->error_message['status'] = '1';
            $this->error_message['data'][] = '验证失败！';
            echo getBack(1, '验证失败！');
            exit;
        }
    }

    protected function getKey($app_id = '') {
        $apps = new Apps();
        $key = $apps->getKey($app_id);
        return $key;
    }

    protected function checkTime($time) {
        if ((time() - $time) > (5 * 60)) {
            $this->error_message['status'] = '1';
            $this->error_message['data'][] = '连接超时！';
            echo getBack(1, '连接超时！');
            exit;
        }
    }

    protected function authentication() {
        $Rbac = new \org\util\Rbac($this->data['uid']);
        $auth_status = $Rbac::AccessDecision();
        if (!$auth_status) {
            echo getBack(1, '当前用户无此操作权限！');
            exit;
        }
    }

    //处理上传文件
    protected function dealwithFile($data) {
        $file_all = request()->file();
        $files = array();
        if($file_all) {
            $file_msg = array();
            if(!empty($data['msglist'])){
                $file_msg = json_decode($data['msglist'], true);
            }
            foreach($file_all as $key => $val) {
                $file_num = substr($key, strpos($key, '_') + 1);
                $summary = '';
                $temp_arr = [];
                foreach($file_msg as $val_msg) {
                    if ($val_msg['file_hash'] == $data['fileinfo']['hash_' . $file_num]) {
                        $temp_arr = $val_msg;
                        unset($temp_arr['file_hash']);
                        unset($temp_arr['summary']);
                        $summary = $val_msg['summary'];
                        break;
                    }
                }
                $temp_arr['file'] = $val;
                $temp_arr['name'] = $data['fileinfo']['name_' . $file_num];
                $temp_arr['hash'] = $data['fileinfo']['hash_' . $file_num];
                $temp_arr['summary'] = $summary;
                $files[$file_num] = $temp_arr;
                $hash_all[$file_num] = $data['fileinfo']['hash_' . $file_num];
            }
            unset($this->data['msglist']);
            unset($this->data['fileinfo']);
            $this->data['upload_files'] = $files;
            $this->data['upload_files_hashs'] = $hash_all;
        }
    }
    /*
     * 分页数据处理，将total，per_page，current_page，last_page放到同一个数组下
    */
    public function dataPageDispose($data) {
        $arr = ['data' => ''];
        $page = array_diff_key($data, $arr);
        $data = ['page' => $page, 'data' => $data['data']];
        return $data;
    }
    //创建auth值
    //可传入要放入auth的一维数组
    public function createAuth($param = false) {
        $jwt = new Jwt();
        $time = time();
        $expTime = $time + (10 * 60);//过期时间为10分钟
        $param['uid'] = $this->data['uid'];
        $param['iat'] = $time;
        $param['exp'] = $expTime;
        $param['nbf'] = $time;
        $param['jti'] = $time;
        $auth = $jwt->getToken($param);
        return $auth;
    }

}
