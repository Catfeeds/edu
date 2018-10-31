<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    //权限相关数据表设置
    'db_prefix' => 'edu_',
    'rbac_role_table' => 'role',
    'rbac_user_table' => 'role_user',
    'rbac_access_table' => 'access',
    'rbac_node_table' => 'node',
    //权限相关配置项
    'user_auth_type' =>1, //认证类型
    'user_auth_type'  => 'uid',//认证识别号
    //require_auth_module // 需要认证模块
    'not_auth_module' => 'Index',//无需认证模块
    'not_auth_action' => '',//无需认证方法
    //user_auth_gateway //认证网关
    'rbac_superadmin' =>'admin',
    'admin_auth_key' => 'superadmin',
    'user_auth_on' => true,//是否认证
    //rbac_db_dsn // 数据库连接DSN
    //权限数据库配置示例
    /*'rbac_db_dsn' => [
        // 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => 'energy123',
        // 数据库用户名
        'username' => 'root',
        // 数据库密码
        'password' => '',
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => 'chuandge_',
    ],*/
    'view_replace_str' => array(
            '__CSS__' => str_replace("//", "/", dirname($_SERVER['SCRIPT_NAME']) . '/public/static/admin/css/'),
            '__JS__' =>  str_replace("//", "/", dirname($_SERVER['SCRIPT_NAME']) . '/public/static/admin/js/'),
            '__IMAGES__' =>  str_replace("//", "/", dirname($_SERVER['SCRIPT_NAME']) . '/public/static/admin/images/')
    ),
    //角色级别
    'role_level' => [
        '3' => '系管理员',
        '5' => '老师',
        '8' => '学生'
    ]

];
