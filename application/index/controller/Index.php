<?php
namespace app\index\controller;

class Index extends Common
{
    public function __construct(){
        parent::__construct();
        $seo = '';
    }

    public function index() {
     //   session("name", "名字1");
        \think\Url::root('index.php');
        return $this->fetch('', ['seo'=>'1111']);
    }

    public function login(){
        return	$this->fetch('', ['seo'=>'用户登录']);
    }

    public function logining(){

    }

    public function logout(){
        session(null);
        return true;
    }


}
