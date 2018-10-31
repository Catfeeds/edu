<?php
namespace app\exam\model;
use think\Model;
class ExamAnswer extends Model
{
    protected function initialize() {
        parent::initialize();
    }
    protected $pk = 'id';
    //课程新增id自动完成
    protected $auto = ['id'];
    protected $insert = ['status','timecreated','timemodified'];  

    protected function setIdAttr($id) {
        return $id ? $id : createGuid();
    }
    protected function setStatusAttr() {
        return 0;
    }
    protected function setTimecreatedAttr() {
        return time();
    }
    protected function setTimemodifiedAttr() {
        return 0;
    }  
    /**
     * 查询答案
     * 传入一批试题id，返回答案，根据传来的字段排序
     * @author 李桐 
     * @param array（） question_id  试题id
     * @param string    order        排序字段
     */
     public function getAnswer($question_id, $order){
        $res = collection($this->where('questionid','in',$question_id)->where('status',0)->order($order)->select())->toArray();
        return $res;
     }

     /**
     * 查询试题信息
     * 传入试卷id，返回当前试卷的所有的题
     * author 冉孟圆 
     */
     public function getExamAnswer ($arr) {
        $res = collection($this->where('questionid','in',$arr)->where('status',0)->field('id,questionid,qtype,answer,fraction,tab,img')->select());
        if ($res) {
            return $res->toArray();
        } else {
            return false;
        }
     }
     /**
     * 查询阅读理解试题信息
     * 传入试卷id，返回当前阅读题的所有的题
     * author 冉孟圆 
     */
     public function getReadExamAnswer ($res) {
        $info = collection($this->where('questionid',$res)->where('status',0)->field('id,questionid,qtype,answer,fraction,tab,img')->select());
        if ($info) {
            return $info->toArray();
        } else {
            return false;
        }
     }

     /**    NEW!!!
     * 查询阅读理解试题信息
     * 传入试卷id，返回当前阅读题的所有的题
     * author 冉孟圆 
     */

     public function getExAnswer($question_id){
        $res = collection($this->where('questionid','in',$question_id)->where('status',0)->select())->toArray();
        return $res;
     }

}