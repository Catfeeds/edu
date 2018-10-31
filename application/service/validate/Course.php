<?php
namespace app\service\validate;
use think\Validate;
class Course  extends  Validate
{
    protected $rule = [
        'fullname' =>'require|/^[\x{4e00}-\x{9fa5}0-9a-zA-Z.#-_]+$/u|max:20',
        'shortname' =>'/^[\x{4e00}-\x{9fa5}0-9a-zA-Z.#-_]+$/u',//|max:10',
        'idnumber' =>'unique:Course|/^[0-9a-zA-Z_]+$/u',
        'numsections'=>'require|gt:0|integer',
        'department' => 'require|/^[0-9A-F]+$/u',
        'category' => 'require|/^[0-9A-F]+$/u',
        'id' => 'require|/^[0-9A-F]+$/u',
        'coursepic'=>'file|fileMime:image/png,image/x-png,image/gif,image/jpeg,image/pjpeg,application/octet-stream|fileExt:jpg,png,gif,psd'
    ];
    protected  $message = [
        'fullname.require' => '课程全称不得为空',
        'fullname./^[\x{4e00}-\x{9fa5}0-9a-zA-Z.#]+$/u'=>'课程全称只能包含汉字、字母、数字、.、#、-、_',
        'fullname.max' => '课程全称不得超过20个字符',
        'shortname.require' => '课程简称不能为空!',
    //    'shortname.max' => '课程简称不能超过10个字符!',
        'shortname./^[\x{4e00}-\x{9fa5}a-zA-Z.#]+$/u' => '课程简称只能包含汉字、字母、数字、.、#、-、_',
        'idnumber.unique' => '课程编号重复',
        'idnumber./^[0-9a-zA-Z_]+$/u' => '课程编号只能由数组字母下划线组成',
        'numsections.require' => '课时数不能为空',
        'numsections.gt' => '课时数必须大于0',
        'numsections.number' => '课时数必须为整数',
        'department.require' => '机构不能为空',
        'department./^[0-9A-F]+$/u' => '机构值不正确',
        'category.require' => '课程类型不能为空',
        'category./^[0-9A-F]+$/u' => '课程类型值不对',
        'coursepic.fileExt' => '请确保图片后缀是png,jpg,gif,psd',
        'coursepic.fileMime' => '上传图片格式错误,支持jpg,png,gif,psd',
        'id./^[0-9A-F]+$/u' => '课程代码错误'
    ];
    protected  $scene = [
      'file' => ['coursepic'],
      'data' => ['fullname', 'shortname', 'numsections' , 'idnumber', 'department'],
      'update' => ['id', 'fullname', 'shortname', 'numsections' , 'idnumber', 'department']
    ];
}
