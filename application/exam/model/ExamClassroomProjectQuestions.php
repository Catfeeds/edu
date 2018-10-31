<?php
namespace app\exam\model;
use think\Model;

class ExamClassroomProjectQuestions extends Model
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
     * 根据场次id查询试题及答案
     * @author 宋玉琪 
     * @return projectid 场次ID
     */
    public function joinAnswerByProjectId($projectid)
    {
        $field = '
        projectquestion.qtype project_questions_qtype,
        projectquestion.score project_questions_score,
        projectquestion.questionid project_questions_questionid,
        projectquestion.seq project_questions_sequencenumer,
        answer.id answer_id,
        answer.answer answer_answer,
        answer.fraction answer_fraction,
        answer.tab answer_tab,
        answer.img answer_img
        ';
        $join = [
            ['exam_answer answer', 'projectquestion.questionid = answer.questionid','left']
        ];
        $res = $this->alias('projectquestion')->field($field)->join($join)->where('projectquestion.projectid', $projectid)->select();
        return $res;
    }
}
