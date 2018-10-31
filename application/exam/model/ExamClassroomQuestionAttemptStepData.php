<?php
namespace app\exam\model;
use think\Model;

class ExamClassroomQuestionAttemptStepData extends Model
{
        protected function initialize() {
        parent::initialize();
    }
    protected $pk = 'id';
    protected $insert = ['id'];
    protected function setIdAttr($val)
    {
        return $val ? $val : createGuid();
    }
}