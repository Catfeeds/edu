<?php
namespace app\exam\model;
use app\service\model\Attachments;
use think\Model;

class ExamQuestion extends Model
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
    protected function getTimecreatedAttr($value) {
        return date("Y-m-d",$value);
    }
    //屏蔽部分字段不被更新
    protected $readonly = ['pid','qtype', 'seq', 'courseid', 'sectionid', 'sectionsid', 'createbyid', 'timecreated'];
    
    /**
     * 查询字段过滤
     * @author 陈博文 
     */
    public function queField($id) {
        $feild_arr = $this->where(['id' => $id, 'status' => 0])->field('qtype,courseid,sectionid,sectionsid,seq')->find()->toArray();
        if (isset($feild_arr) && !empty($feild_arr)) {
            return $feild_arr;
        } else {
            return false;
        }
    }
    /**
     * 获取问题详情
     * @author 宋玉琪 
     * @param $quesid  问题的ID
     */
    public function getQuesbyid($quesid)
    {
        $join = [
            ['course c', 'c.id = q.courseid', 'LEFT'],
            ['user u', 'u.id = q.createbyid', 'LEFT']
        ];
        $where = [
            'q.id' => $quesid,
            'q.status' => '0',
            'c.status' =>'1',
            'u.status' => '0'
        ];
        $field = "q.id, q.qtype, c.fullname as course, q.sectionid as section, q.sectionsid as sections, q.difficulty as diff, q.usepermise, q.name, q.questiontext, q.generalfeedback, u.username as auth, q.timecreated as createtime, q.img as imgs, q.check";
        $res = $this->alias('q')->join($join)->where($where)->field($field)->find();
        return $res;
    }
    /**
     * 根据父问题id获取题目
     * @author 宋玉琪 
     * @param $pid  父问题id
     */
    public function getQuesbypid($pid)
    {
        $res = $this->where('status', '0')->where('pid', $pid)->select();
        return $res;
    }
    /**
     * 根据父ID 查询问题及答案
     * @author 宋玉琪 
     * @param pidarray  PID数组
     */
    public function joinAnswerInPid($pidarray)
    {
        $field = '
        question.qtype question_qtype,
        question.id questions_id,
        question.seq questions_sequencenumer,
        question.pid questions_pid,
        answer.id answer_id,
        answer.answer answer_answer,
        answer.fraction answer_fraction,
        answer.tab answer_tab,
        answer.img answer_img
        ';
        $join = [
            ['exam_answer answer', 'question.id = answer.questionid'],
        ];
        $res = $this->alias('question')->join($join)->field($field)->where('pid', 'in', $pidarray)->select();
        return $res;
    }
    /**
     * 问题删除
     * @author 宋玉琪 
     * @param $data  前端总数据
     */
    public function deleteQuesbyid($data)
    {
        $this->data([
            'id' => $data['id'],
            'status' => '1',
            'modifiedbyid' => $data['uid'],
            'timemodified' => time()
        ]);
        $res = $this->save();
        return $res;
    }
    /**
     * 问题审核
     * @author 宋玉琪 
     * @param $data  前端总数据
     */
    public function checkQuestion($data)
    {
        $res = $this->save(['check' => $data['status']],['id' => $data['id']]);
        return $res;
    }
    /**
     * 根据前端试题id更新试题审核转态
     * @author 冉孟圆
     * @param  $id  试题id
     */
    public function questionError($id) {
        if ($id) {
            $result = $this->where('id',$id)->update(['check' => "3"]);
            return getBack(0,true);
        } else {
            return getback(1,false);
        }
    }
    /**
     * 万能查询
     * @author 宋玉琪 
     * @param $where 查询调和
     */
    public function searchQuesByWhere($where)
    {
        $res = $this->where($where)->select();
        return $res;
    }

    /**
     * 查询试题
     * author 冉孟圆
     */
    public function getQuizQuestionList($res) {
        $res = $this->where('pid',$res)->field('name,questiontext,img')->find();
        if ($res) {
            return $res->toArray();
        } else {
            return false;
        }
    }

}