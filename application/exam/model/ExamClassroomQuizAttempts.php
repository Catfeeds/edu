<?php
namespace app\exam\model;
use think\Model;

class ExamClassroomQuizAttempts extends Model
{
        protected function initialize() {
        parent::initialize();
    }
    protected $pk = 'id';
    /**
     * 自动设置ID
     * @author 宋玉琪
     */
    protected $insert = ['id', 'status' => 0, 'finished' => 0, 'timestart', 'layout' => '1,'];
    protected function setIdAttr($val)
    {
        return $val ? $val : createGuid();
    }
    protected function setTimestartAttr($val) {
        return $val ? $val : time();
    }

}