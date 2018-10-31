<?php
namespace app\exam\model;
use think\Model;

class ExamClassroomQuiz extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }
    protected $pk = 'id';
   //修改时间的更新
    protected $autoWriteTimestamp = true;
    protected $createTime = 'timecreated';
    protected $updateTime = false;
    protected $insert = ['id','timemodified'];
    protected $update = ['timemodified'];
    protected $type = [
        'timeopen' => 'timestamp',
        'timeclose' => 'timestamp',
    ];

    protected function setTimelimitAttr($time)
    {
        return $time * 60;
    }
    protected function setTimemodifiedAttr()
    {
        return time();
    }
    protected function setIdAttr($val)
    {
        return $val ? $val : createGuid();
    }
    protected function getTimeopenAttr($time){
        return date("Y-m-d H:i", $time);
    }
    protected function getTimelimitAttr($time){
        $s = $time % 60;
        $m = intval($time / 60);
        return $m . '分' . $s . '秒';
    }
    protected function getTimecloseAttr($time){
        return date("Y-m-d H:i", $time);
    }
    protected function getTimecreatedAttr($time){
        return date("Y-m-d H:i", $time);
    }
    protected function getTimemodifiedAttr($time){
        return date("Y-m-d H:i", $time);
    }
    protected function getCheckAttr($val){
        return $this->checkcode[$val];
    }
    protected function getStateAttr($val){//将check转换为state时，此方法用
        return $this->checkcode[$val];
    }
    protected function getCheckcodeAttr(){//实例化对象得到状态码信息
        return $this->checkcode;
    }
    //状态码，需要和数据库保持一致
    protected $checkcode = [1 => '创建成功', 2 => '创建中', 3 => '考试中', 4 => '考试结束', 5 => '审核中', 6 => '审核通过', 7 => '审核未通过'];
    protected function getExamtypeAttr($val) {
        $value = [0 => '测试', 1 => '考试'];
        return $value[$val];
    }
    protected function getDisparktimeAttr($val) {//组合开始、结束时间时使用
        $arr = explode(',', $val);
        if (!$arr[0] || !$arr[1]) {
            return '开始结束时间未定！';
        }
        return date('Y-m-d H:i', $arr[0]) . '开始' .date('Y-m-d H:i', $arr[1]) . '结束';
    }
    /**
     * @param $data
     * @return false|\PDOStatement|string|\think\Collection
     * @User: 宋玉琪
     * @type: 试卷列表条件查询
     */
    public function getQuizList($where, $page = false, $rows = false)
    {
        $join = [
            ['__CLASSROOM__ c', 'c.id = ecq.classroomid'],
            ['__USER__ user', 'user.id = ecq.createdbyid']
        ];
        $field = 'ecq.id, ecq.name, c.fullname as classroom, ecq.check as state, ecq.check as state_code, ecq.timecreated, concat_ws(",", ecq.timeopen, ecq.timeclose) as disparktime, ecq.timeopen, ecq.timeclose, ecq.examtype, user.realname as auth, ecq.timelimit, ecq.grade';
        $where['c.status'] = 0;
        $result = $this->alias('ecq')->where($where)->join($join)->field($field)->paginate($rows, false, ['page' => $page]);
        return $result;
    }
    /*
     * 查询试卷审核状态
     */
    public function selectQuiz($id){
        $se = $this ->where('id',$id)->value('check');
        return $se;
    }

    /*
     * 查询试卷详情
     */
   public function quizDetail($id) {   
        $res = $this->where('id', $id)->where('status','=',0)->find();
        return $res;
    }
    /*
    * 查询试卷
    */
    public function quizcopy($id) {
        $fi = ['id','name','classroomid','sections','intro','examtype','timeopen','timeclose','timelimit','preferredbehaviour','grademethod','attempts','shufflequestions','grade'];
        $res = $this->where('id', $id)->field($fi)->find();
        if ($res) {
            return $res = $res->toArray();
        } else {
            return false;
        }
    }
}