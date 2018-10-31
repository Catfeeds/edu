<?php
namespace app\service\validate;
use think\Validate;
class Validates  extends  Validate
{
    protected $rule = [];//规则
    protected $message = [];//错误信息
    public function __construct($name) {
        parent::__construct();
        switch ($name) {
            case 'pro'://专业
                $this->rule = [
                    'name' => 'require|max:150',
                    'code' => 'require|max:100',
                    'describe' => 'require',
                    'organization' => 'require|/^[0-9A-F]+$/u|max:32'
                ];
                $this->message = [
                    'name.max' => '名字过长',
                    'code.max' => '编号过长',
                    'organization.max' => '机构信息错误',
                    'name./^[0-9A-F]+$/u' => '机构信息错误'
                ];
                break;
            case 'classroom'://课堂
                $this->rule = [
                    'fullname' => 'require|max:300',
                    'shortname' => 'require|max:300',
                    'idnumber' => 'require|max:100|alphaDash',
                    'numsections' => 'require|integer',
                    'category' => 'require|/^[0-9A-F]+$/u|max:32',
                    'department' => 'require|/^[0-9A-F]+$/u|max:32',
                    'summary' => 'max:500',
                    'course_id' => '/^[0-9A-F]+$/u|max:32',
                    'semester' => 'require|number'
                ];
                $this->message = [
                    'fullname.max' => '名字过长',
                    'shortname.max' => '简称过长',
                    'numsections.integer' => '课时请输入数字',
                    'idnumber.max' => '编号过长',
                    'idnumber.alphaDash' => '课堂编号为字母和数字，下划线 _ 及破折号 - ',
                    'category.max' => '课堂类型错误',
                    'category./^[0-9A-F]+$/u' => '课堂类型错误',
                    'department./^[0-9A-F]+$/u' => '机构数据错误',
                    'department./^[0-9A-F]+$/u' => '机构数据错误',
                    'summary.max' => '课堂描述过长',
                    'course_id./^[0-9A-F]+$/u' => '课程信息错误',
                    'course_id.max' => '课程信息错误',
                    'semester.require' => '课堂学期信息缺少',
                    'semester.number' => '课堂学期信息为数字'
                ];
                break;
            case 'assignment'://作业
                $this->rule = [
                    'name' => 'require|max:150',
                    'explain' => 'require|max:100',
                    'type' => 'require|/^[0-9A-F]+$/u|max:32',
                    'courseid' => '/^[0-9A-F]+$/u|max:32'
                ];
                $this->message = [
                    'name.max' => '名字过长',
                    'type.max' => '作业类型错误',
                    'type./^[0-9A-F]+$/u' => '作业类型错误',
                    'explain.max' => '作业描述过长',
                    'courseid./^[0-9A-F]+$/u' => '课程信息错误',
                    'courseid.max' => '课程信息错误'
                ];
                break;
        }
    }
}