<?php
namespace app\admin\controller;
use think\Controller;
use \driver\jwt\Jwt;

class Common extends \think\Controller {
    protected $defaultareaid;
    public $data = false;
    public function __construct($auth = false) {
        parent::__construct();
        $this->data = json_decode(\think\Request::instance()->param('data'), true);
        if (!session('uid')) {
            $jwt = new Jwt();
            if ($auth) {
                $param = $jwt->verifyToken($auth);
                if (!$param) {
                    return false;
                }
                session('uid', $param['uid']);
                $this->data['uid'] = $param['uid'];
            } else {
                $authorization = Request()->header('Authorization');
                $payload_request = $jwt->verifyToken($authorization);
                if (!$payload_request) {
                    $this->redirect('@index/Index/login');
                }
                session('uid', $payload_request['uid']);
                $this->data['uid'] = $payload_request['uid'];
            }
        }

     //   $notAuth = in_array(Request()->module(), explode(',', config('not_auth_module'))) || in_array(Request()->action(), explode(',', config('not_auth_action')));
            //判定用户的权限
        if(config('user_auth_on')){
            $Rbac = new \org\util\Rbac(session('uid'));
     //       $Rbac::AccessDecision() || $this->error("没有权限");
        }
    }



}