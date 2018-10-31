<?php
namespace app\index\controller;

class Main extends Common{
    // 主页 vue
    public function index(){
        return    $this->fetch('main/main', ['seo'=>'用户登录创智云教育平台']);
    }
    // 系统管理 -》 课程库 vue
    public function library(){
        return    $this->fetch('main/library', ['seo'=>'库管理中心']);
    }
    // 系统管理 -》 用户中心   专业管理   班级管理
    public function management(){
        return    $this->fetch('main/management', ['seo'=>'管理中心']);
    }
    // 系统管理 -》 机构中心
    /* public function organization(){
        return    $this->fetch('main/organization', ['seo'=>'机构中心']);
    } */
    
}