<?php
namespace app\exam\validate;
use think\Validate;
class ExamClassroomQuestionAttemptStep extends Validate
{
    protected  $rule = [
        'grade' => 'require|number'
    ];
    protected $message = [
        'grade.number'   => '分数必须是数字',
        'grade.require'  => '分数必须'
    ];
}