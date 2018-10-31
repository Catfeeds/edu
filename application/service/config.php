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
    //
    'sections_param' => 'dataList', //课程章节共有参数名  
    'parentstr_prefix' => '0,', //节点树路径前缀
    'pid_default' => 0,//pid默认值
    'user_list' => ['uid', 'username', 'idnumber', 'pid', 'address', 'picture', 'description', 'education', 'enter_time', 'logintime', 'access_list'],
    //'rbac_db_dsn' => '', 数据库连接dsn
    'db_prefix' => 'edu_',
    'rbac_role_table' => 'role', //角色表名称
    'rbac_user_table' => 'role_user', //角色用户表名称
    'rbac_access_table' => 'access', //权限表名称
    'rbac_node_table' => 'node', //节点表名称

    //'rbac_superadmin' => 'admin', //超级管理员账号
    'admin_auth_key' => 'superadmin', //管理员认证识别号
    'user_auth_key' => 'uid', //认证识别号
    'user_auth_type' => 1, //认证类型
    //权限需要时开启
    'not_auth_module' => 'INDEX,', //无需认证模块
    'guest_auth_on' => false, //游客认证是否需要
    /*'require_auth_module' => '', //需要认证模块
    'not_auth_action' => '', //无需认证方法
    'require_auth_action' => '', //需要认证方法
    'guest_auth_id' => '', //游客认证id
    'user_auth_gateway' => '', //认证网关
    'admin_auth_key' => '', //用户认证模块*/
    'user_auth_on' => true, //是否需要认证

    //图片配置
    'picture_width' => 800,
    'picture_height' => 800,

    

];
