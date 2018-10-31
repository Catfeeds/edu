<?php
namespace app\index\controller;
use think\Controller;

class Common extends \think\Controller {
    public function __construct(){
        parent::__construct();
        $seo = '';
        //相关页面权限设置
        $requestController = strtoupper(request()->controller());
        $controller['no'] = explode(',', config('not_auth_module'));
        if (!$controller['no'] || !in_array($requestController, $controller['no'])) {
            $this->checkLogin();
        } else {
            
        }
    }

    protected function checkLogin() {
        //后面页面权限控制
        if (!session('uid')) {
            $this->redirect('@index/Index/login');
        }
    }
}
