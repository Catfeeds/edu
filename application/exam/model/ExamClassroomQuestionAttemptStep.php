<?php
namespace app\exam\model;
use think\Model;

class ExamClassroomQuestionAttemptStep extends Model
{
    protected function initialize() 
    {
        parent::initialize();
    }
    protected $pk = 'id';
    protected $insert = ['id'];
    protected function setIdAttr($val)
    {
        return $val ? $val : createGuid();
    }
    public function joinAnswerByAttempId($attemptid)
    {
        $field = '
        step.id step_id,
        step.sequencenumer step_sequencenumer,
        step.pid step_pid,
        step.score step_score,
        question.qtype question_type,
        question.questiontext question_questiontext,
        question.img question_img,
        answer.id answer_id,
        answer.answer answer_answer,
        fraction answer_fraction,
        answer.tab answer_tab,
        answer.img answer_img
        ';
        $join = [
            ['exam_question question', 'step.questionid = question.id'],
            ['exam_answer answer', 'question.id = answer.questionid','left'],
        ];
        $res = $this->alias('step')->field($field)->join($join)->where('step.attempts', $attemptid)->select();
        return $res;
    }
    public function UpdateMark($where, $data){
        $re['status'] = $this->validate(true)->allowField(['remark', 'grade'])->save($data, $where);
        if(false === $re['status']) {
            $re['error_message'] = $this->getError();
            return $re;
        }
        return  $re;
}
}