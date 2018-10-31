<?php
namespace app\service\validate;
use think\Validate;
class User  extends  Validate
{
    protected $rule = [
        'id'            => 'require',
        'username'      => 'require|max:100|unique:user, statue, 0',
        'realname'      => 'require|max:50',
        'password'      => 'max:50|alphaDash',
        'checkpassword' => 'requireWith:password|confirm:password',
        'idnumber'      => 'require|max:100',
        'email'         => 'email',
        'phone'         => '/^1[34578]\d{9}$/|unique:user, statue, 0',
        'address'       => 'max:300',
        'description'   => 'max:300',
        'education'     => 'require|number|between:1,10',
        'sex'           => 'require|number|in:1,2',
        'org_id'        => 'require|/^[0-9A-Z]+$/|max:32',
        'enter_time'    => '/^[0-9]\d{9}/|max:10',
        'class'         => '/^[0-9A-Z]+$/|max:32'
    ];
    protected  $message = [
        'username.max'            => '用户名过长',
        'username.unique'         => '该用户已注册，可直接使用用户名登录！',
        'realname.max'            => '用户名字过长',
        'password.max'            => '密码过长',
        'password.alphaDash'      => '密码为字母和数字，下划线 _ 及破折号 -',
        'checkpassword'           => '两次密码输入不相符',
        'idnumber.max'            => '用户编码过长',
        'email.email'             => '电子邮件格式不正确',
        'phone./^1[34578]\d{9}$/' => '电话号码不正确',
        'phone.unique'            => '该手机号已经注册，可使用该手机号直接登录！',
        'address.max'             => '地址信息过长',
        'description.max'         => '个人描述过长',
        'education.number'        => '学历不正确',
        'education.between'       => '学历不正确',
        'sex.number'              => '性别不正确',
        'sex.in'                  => '性别不正确',
        'org_id.max'              => '机构信息错误',
        'org_id./^[0-9A-Z]+$/'    => '机构信息错误',
        'enter_time.max'          => '入学时间/入职时间错误',
        'enter_time./^[0-9]\d{9}/'=> '入学时间/入职时间错误',
        'class.max'               => '班级信息错误',
        'class./^[0-9A-Z]+$/'     => '班级信息错误'
    ];
    protected $scene = [
        'edit' => ['id', 'username', 'realname', 'password', 'checkpassword', 'email', 'phone', 'address', 'description', 'education', 'sex', 'enter_time', 'class'],
        'add'  => ['username', 'realname', 'password' => 'require|max:50|alphaDash', 'checkpassword', 'idnumber', 'email', 'phone', 'address', 'description', 'education', 'sex', 'org_id', 'enter_time', 'class']
    ];
}