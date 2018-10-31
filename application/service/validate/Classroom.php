<?php
namespace app\service\validate;
use think\Validate;
class Classroom  extends  Validate
{
    protected $rule = [
            'id' => 'require|length:32',
            'category' => 'length:32|alphaNum',
            'numsections' => 'number',
    ];
    protected  $message = [
        'id.require' => '传入参数丢失！',
        'id.length'=>'传入参数错误！',
        'category.length' => '机构错误！',
        'category.alphaNum' => '机构错误！',
        'numsections.number' => '课时为数字！'
    ];
    protected $scene = [
        'edit' => ['id', 'category', 'numsections'],
        'add'  => []
    ];
}