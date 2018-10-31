<?php
namespace app\exam\model;
use think\Model;

class ExamClassroomQuizQuestions extends Model
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
    /**
     * 查找题目的当前位置
     * @author 宋玉琪
     */
    public function findSeq($where)
    {
        $seq = $this->where($where)->max('seq');
        return $seq;
    }
    /**
     * 通过where条件删除试卷题目
     * @author 宋玉琪
     * @param $where 删除的条件
     */
    public function delQuestsByWhere($where)
    {
        $res = $this->where($where)->delete();
        return $res;
    }
    
    /**
     * 查询试题
     * author 冉孟圆
     */
    public function getQuizQuestionList($quiz) {
        $res = collection($this->where('quiz',$quiz)->select());
        if ($res) {
            return $res->toArray();
        } else {
            return false;
        }
    }
    /**
     * 查询试题答案
     * author 冉孟圆
     */
    public function getAnswer($res) {
        $res = $this->where('questionid',$res)->find();
        if ($res) {
            return $res->toArray();
        } else {
            return false;
        }
    }
    /**
     * 根据试卷id查询试题及答案
     * @author 宋玉琪 
     * @return quizid 场次ID
     */
    public function joinAnswerByQuizId($quizid)
    {
        $field = '
        quizquestion.qtype project_questions_qtype,
        quizquestion.score project_questions_score,
        quizquestion.questionid project_questions_questionid,
        quizquestion.seq project_questions_sequencenumer,
        answer.id answer_id,
        answer.answer answer_answer,
        answer.fraction answer_fraction,
        answer.tab answer_tab,
        answer.img answer_img
        ';
        $join = [
            ['exam_question question', 'quizquestion.quiz = question.id'],
            ['exam_answer answer', 'question.id = answer.questionid','left'],
        ];
        $res = $this->alias('quizquestion')->field($field)->join($join)->where('quizquestion.quiz', $quizid)->select();
        return $res;
    }

}