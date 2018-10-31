<?php
namespace app\service\validate;
use think\Validate;
class Classes  extends  Validate
{
    protected $rule = [
        'name' =>'require|/^[\x{4e00}-\x{9fa5}0-9a-zA-Z]+$/u|max:20',
        'department' => 'require|/^[0-9A-F]+$/u',
        'number'=>'number',
        'code'=>'require|alphaDash|max:10',
    ];
    protected  $message = [
        'name.require' => '班级名称不得为空',
        'name./^[\x{4e00}-\x{9fa5}0-9a-zA-Z]+$/u'=>'课程全称只能包含汉字、字母、数字',
        'name.max' => '班级全称不得超过20个字符',
        'department.require' => '机构不能为空',
        'department./^[0-9A-F]+$/u' => '机构值不正确',
        'number.number'=>'班级人数必须是数字',
        'code.alphaDash'=>'班级编号必须是字母、数字、下划线',
        'code.max' => '班级编号不能超过10位'
    ];
    protected $scene = [
        'edit' => ['name', 'number'],
        'add'  => ['name', 'department', 'number', 'code']
    ];
}