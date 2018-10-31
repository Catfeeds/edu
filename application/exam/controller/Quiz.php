<?php
namespace app\exam\controller;

use app\service\controller\Common;
use app\service\model\ClassroomUser;
use app\service\model\Classroom;
use app\service\model\ClassroomSections;
use app\service\model\User;
use app\exam\model\ExamClassroomProject;
use app\exam\model\ExamClassroomQuizQuestions;
use app\exam\model\ExamAnswer;
use app\exam\model\ExamClassroomQuiz;
use app\admin\model\Role;

class Quiz extends Common
{
    public function __construct(){
        parent::__construct();
    }
    /**
     * @return mixed
     * @type:试卷列表
     * @author 宋玉琪
     */
    public function getQuizList()
    {
        $data = $this->data;

        if (!isset($data['classroom']) && empty($data['classroom']))
            return getBack(1, '参数错误');
        $role = new Role();
        $userRole = $role->getUserRoleLevel($data['uid']);
        if ($userRole > 1) {
            $where = [
                'user_id' => $data['uid'],
                'classroom_id' => $data['classroom']
            ];
            $classroomUser = new ClassroomUser();
            $userRole = $classroomUser->where($where)->value('role');
        } else {
            $userRole = 0;
        }
        if ($userRole == 1)
            return getBack(1, '该登录用户无此查看权限！');
        $quiz = new ExamClassroomQuiz();
        $where = ['ecq.classroomid' => $data['classroom'], 'ecq.status' => 0];
        $page = 1;
        $rows = config('paginate.list_rows');
        if (@!empty($data['keywords']))
            $where['ecq.name'] = array('like', '%' . $data['keywords'] . '%');
        if (@!empty($data['examtype'])) {
            if (!is_numeric($data['examtype'])) {
                return getBack(1, '试卷用途参数错误！');
            }
            $where['ecq.examtype'] = $data['examtype'];
        }
        if (@!empty($data['check'])) {
            if (!array_key_exists($data['check'], $quiz->checkcode)) {
                return getBack(1, '试卷状态参数错误！');
            }
            $where['ecq.check'] = $data['check'];
        }
        if (@!empty($data['page']) && is_numeric($data['page']) && $data['page'] > 0)
            $page = $data['page'];
        if (@!empty($data['rows']) && is_numeric($data['rows']) && $data['rows'] > 0)
            $rows = $data['rows'];
        $res = $quiz->getQuizList($where, $page, $rows)->toArray();
        if (empty($res['data']))
            return getBack(1, '暂无数据！');
        $res = $this->dataPageDispose($res);
        return getBack(0, $res);
    }
    /**
     * 创建试卷
     * @author 宋玉琪 
     */
    public function createQuiz()
    {
        $data = $this->data;
        $quiz = model('ExamClassroomQuiz');
        $questions = array();
        if(isset($data['questions']) && ! empty($data['questions']))
        {
            $questions = $data['questions'];
            $data['questions'] = '';
        }
        
        if($data['examtype'] == 1)
        $data['attempts'] = 1;
        $data['classroomid'] = $data['classroom'];
        $data['sections'] = implode(',', array_merge($data['sections'], $data['section']));
        $data['createdbyid'] = $data['uid'];
        $data['check'] = 2;
        $res = $quiz->allowField(true)->save($data, false);
        if($res)
        {
            if($data['shufflequestions'] == 0)
            {
                if(empty($questions))
                {
                    return getBack(1, "没有试题配置");
                }
                $data = [
                    'opt' => 1,
                    'uid' => $data['uid'],
                    'classroom' => $data['classroomid'],
                    'quiz' => $quiz->id,
                    'questions' => $questions
                ];
                $res = $this->autoAddQuestion($data);
                if($res['status'] == 0)
                {
                    return getBack(0, ['quiz' => $quiz->id]);
                }
                else return getBack(1, $res['data']);
            }
            return getBack(0, ['quiz' => $quiz->id]);
        }
        return getBack(1, "创建试卷失败");
    }
    /**
     * 手动添加试题
     * @author 宋玉琪 
     */
    public function handAddQuestion()
    {
        $data = $this->data;
        $quizquest = model('ExamClassroomQuizQuestions');
        $where['quiz'] = $data['quiz'];
        $qtypes = [];
        $qtypesSeq = [];
        foreach ($data['questions'] as $item)
        {
            if(!isset($qtypes[$item['qtype']]))
            {
                $qtypes[$item['qtype']] = 1;
                $qtypesSeq[$item['qtype']] = 0;
            }else{
                $qtypes[$item['qtype']] += 1;
            }
        }
        foreach($qtypes as $key => $val)
        {
            $where['qtype'] = $key;
            $qtypesSeq[$key] = $quizquest->findSeq($where);
        }
        $info = array();
        foreach ($data['questions'] as $main)
        {
            foreach($main['question'] as $item)
            {
                $arr = array();
                $arr['quiz'] = $data['quiz'];
                $arr['questionid'] = $item;
                $arr['qtype'] = $main['qtype'];
                $arr['score'] = $main['score'];
                $arr['seq'] = ++$qtypesSeq[$main['qtype']];
                array_push($info, $arr);
            }
        }
        $res = $quizquest->allowField(true)->saveAll($info, false);
        if($res)
        {
            return getBack(0, $res);
        }else{
            return getBack(1, "试题添加失败");
        }
    }
    /**
     * 自动添加试题
     * @author 宋玉琪
     * @param opt 1为添加，2为编辑 
     */
    public function autoAddQuestion($data)
    {
        $list = array();
        $classroom = new Classroom();
        $opt = $data['opt'];
        $where = [
            'id' => $data['classroom']
        ];
        $courseid = $classroom->where($where)->value('course_id');
        if(is_null($courseid))
        return ['status' => 1, 'data' => '该课堂继承的课程不存在，请联系管理员！'];
        $quest = model('ExamQuestion');
        $where = [
            'status' => 0,
            'check' => 1,
            'courseid' => $courseid
        ];
        $base = array();
        $uid = $data['uid'];
        if (count($data['questions']) > count(config('q_type'))) {
            return ['status' => 1, 'data' => '配置错误，请重新编辑试卷，配置试题！'];
        }
        foreach($data['questions'] as $item)
        {
            $bool = (! isset($item['section']) || empty($item['section'])) || 
                    (! isset($item['sections']) || empty($item['sections'])) || 
                    (! isset($item['qtype']) || empty($item['qtype'])) || 
                    (! isset($item['score']) || empty($item['score'])) || 
                    (! isset($item['conf']) || empty($item['conf']));
            if($bool)
            return ['status' => 1, 'data' => '试题配置错误，请重新编辑试卷，配置试题！'];
            $where['sectionid'] = ['in', $item['section']];
            $where['sectionsid'] = ['in', $item['sections']];
            $where['qtype'] = $item['qtype'];
            $base[$item['qtype']] = $quest->where($where)->where(function($query)use($uid){
                $query->where('usepermise','in', [0, 2])->whereOr(function($query)use($uid){
                    $query->where('usepermise', 1)->where('createbyid', $uid);
                });
            })->select();
        }
        $needquestions = array();
        foreach($data['questions'] as $item)
        {
            if(empty($base[$item['qtype']]))
            continue;
            $num = 0;
            $seq = 0;
            foreach($item['conf'] as $it)
            {
                $bool = (! isset($it['diff']) || empty($it['diff'])) || 
                        (! isset($it['num']) || empty($it['num']));
                if($bool)
                return ['status' => 1, 'data' => '试题配置缺少难度系数或数量，请重新编辑试卷，配置试题！'];
                $qtypequestions = $base[$item['qtype']];
                $qtypediffquestions = array();
                foreach($qtypequestions as $i)
                {
                    if($i['difficulty'] == $it['diff'])
                    {
                        array_push($qtypediffquestions, $i);
                    }
                }
                if(count($qtypediffquestions) < $it['num'])
                {
                    return ['status' => 1, 'data' => "题型为:" . config('q_type.' . $item['qtype']) . ', 难度为:' . config('q_difficulty.' . $it['diff']) . '的试题数量为' . count($qtypediffquestions) . '道， 不满足配置条件，请重新编辑试卷，配置试题！'];
                }
                $needkeys = array_rand($qtypediffquestions, $it['num']);
                if(! is_array($needkeys))
                {
                    $needkeys = array($needkeys);
                }
                foreach($needkeys as $n)
                {
                    $arr = [
                        'quiz' => $data['quiz'],
                        'questionid' => $qtypediffquestions[$n]['id'],
                        'qtype' => $qtypediffquestions[$n]['qtype'],
                        'score' => $item['score'],
                        'seq' => ++$seq
                    ];
                    array_push($needquestions, $arr);
                }
                $num += $it['num'];
                $list[$item['qtype']][$it['diff']] = $it['num'];
            }
            $list[$item['qtype']]['score'] = $item['score'] * $num;
        }
        $examclassroomquizquestions = model('ExamClassroomQuizQuestions');
        if($opt == 2)
        {
            $deltypes = array();
            foreach($data['questions'] as $item)
            {
                array_push($deltypes, $item['qtype']);
            }
            $where = [
                'quiz' => $data['quiz'],
                'qtype' => ['in', $deltypes]
            ];
            $examclassroomquizquestions->delQuestsByWhere($where);
        }
        $res = $examclassroomquizquestions->saveAll($needquestions, false);
        if($res)
        {
            $questions = json_encode($list);
            $quiz = model('ExamClassroomQuiz');
            $data = [
                'id' => $data['quiz'],
                'questions' => $questions,
                'check' => 1
            ];
            $res = $quiz->allowField(true)->isUpdate(true)->save($data);
            if($res)
            return ['status' => 0, 'data' => '自动添加成功'];
            return ['status' => 1, 'data' => '写入questions字段失败'];
        }
        else return ['status' => 1, 'data' => '写入表失败'];
    }
      /**
     * 试卷申请审核
     * @author 李存亮
     */
    public function applyAudit() {
        $data = $this->data;
        $examClassroomQuiz = new ExamClassroomQuiz();
        $check = $examClassroomQuiz->where('id',$data['id'])->value('check');
        //check为1等于创建成功,然后转化为check为5等于审核中
        if ($check == 1) {
             $exchange = $examClassroomQuiz->save([
                        'check' => 5,
                        'uploadbyid' => $data['uid']
                    ],['id' => $data['id']]);
            if($exchange){
                return getBack(0, '成功');
            }
            else{
                return getBack(1, '申请失败');
            }
        }
        else{
            return getBack(1, '试卷创建成功才能申请审核');
        }
    }
  /**
     * 试卷审核
     * @author 李存亮
     */
 public function audit() {
        $data = $this->data;
        $examClassroomQuiz = new ExamClassroomQuiz();
        $check = $examClassroomQuiz->where('id',$data['id'])->value('check');
        if ($check == 5) {
            switch ($data['status']) {
                case 0:
                    $exchange = $examClassroomQuiz->save([
                        'check' => 6,
                        'uploadbyid' => $data['uid']
                    ],['id' => $data['id']]);
                    if($exchange){
                        return getBack(0, '成功');
                    }
                    else{
                        return getBack(1, '审核失败');
                    }
                    break;               
                case 1:
                    $exchange = $examClassroomQuiz->save([
                        'check' => 7,
                        'msg' => $data['msg'],
                        'uploadbyid' => $data['uid']
                    ],['id' => $data['id']]);
                    if($exchange){
                        return getBack(0, '试卷审核未通过');
                    }
                    else{
                        return getBack(1, '审核失败');
                    }
                    break;
            }
            
        }
        else{
            return getBack(1, '试卷未处于审核中');
        }

    }
    /*
     * 试卷软删除
     * @author 李存亮
     */
    public function delQuiz() {
        $data = $this->data;
        $examClassroomQuiz = model('ExamClassroomQuiz');
        $info = $examClassroomQuiz->where('id', $data['id'])->field('status,examtype')->find();
        if ($info['status'] == 0) {
            $res = $examClassroomQuiz->save(['status'=>1,'uploadbyid'=>$data['uid']],['id'=>$data['id']]);
            $quiz = $info->getData('examtype');
            if ($quiz == 1) {
                $examClassroomProject = new ExamClassroomProject();
                $res = $examClassroomProject->save(['status'=>1], ['quiz'=>$data['id']]);
            }
            if ($res !== false) {
                return getback(0, true);
             } else {
                return getback(1, false);
            }
        } else {
            return getback(1,'试卷不存在');
        }
    }
     /**
     * 试卷编辑
     * @author 宋玉琪
     */
    public function editQuiz()
    {
        $data = $this->data;
		$quiz = model('ExamClassroomQuiz');
        $info = $quiz->where('id', $data['id'])->find();
        if($info['status'] == 5)
        return getBack(1,'试卷审核中，不能编辑');
        $data['sections'] = implode(',', array_merge($data['sections'], $data['section']));
        $questions = array();
        if(isset($data['questions']) && ! empty($data['questions']))
        {
            $questions = $data['questions'];
            $data['questions'] = '';
        }
        if($data['examtype'] == 0)
        {
            $res = $quiz->allowField(true)->isUpdate(true)->save($data);
            if($res)
            {
                if($data['shufflequestions'] == 0)
                {
                    if(empty($questions))
                    {
                        return getBack(1, "没有试题配置");
                    }
                    $data = [
                        'opt' => 2,
                        'uid' => $data['uid'],
                        'classroom' => $info['classroomid'],
                        'quiz' => $data['id'],
                        'questions' => $questions
                    ];
                    $res = $this->autoAddQuestion($data);                
                    if($res['status'] == 0)
                    return getBack(0, true);
                    return getBack(1, false);
                }
                return getBack(0, true);
            }
            return getBack(1, false);
        }
    }
    /*
     * 试卷详情
     * @author 冉孟圆
     */
    public function quizDetail() {
        $data = $this->data;
        $quiz = model('ExamClassroomQuiz');
        $res = collection($quiz->quizDetail($data['id'])->getData())->toArray();
        if (!$res) {
            return getBack(1, '未查到相关数据');
        }
        $id = $res['classroomid' ];
        $limit = $res['timelimit'];
        $timelimit = floor($limit/60);  
        $info = $res['sections'];
        $arr = explode(',', $info);
        $classroomSections = new ClassroomSections();
        $sections = collection($classroomSections
                  ->where('id', 'in', $arr)
                  ->where('status', '=', 0)
                  ->field('id, name, type')
                  ->select())->toArray();
        $res['section'] = '';
        $res['sections'] = '';
        $res['sectionname'] = '';
        $res['sectionsname'] = '';
        if ($sections) {
            for ($i=0; $i <count($sections) ; $i++) {
                if($sections[$i]['type'] == 0){
                    $arr1[] = $sections[$i]['name'];
                    $res['sectionname'] = $arr1;
                    $arr2[] = $sections[$i]['id'];
                    $res['section'] = $arr2;
                }
                if($sections[$i]['type'] == 1){
                    $arr3[] = $sections[$i]['name'];
                    $res['sectionsname'] = $arr3;
                    $arr4[] = $sections[$i]['id'];
                    $res['sections'] = $arr4;
                }
            }           
        }
        $array = array('createdbyid' =>$res['createdbyid'] , 'uploadbyid' =>$res['uploadbyid'] );
        $user = new User();
        $think = collection($user->where('id', 'in', $array)->where('status', 0)->field('id, realname')->select())->toArray();
        $res['createdbyid'] = '';
        $res['uploadbyid'] = '';
        if ($think) {
            for ($i = 0; $i < count($think); $i++) {
                if ($think[$i]['id'] == $array['createdbyid']) {
                    $res['createdbyid'] = $think[$i]['realname'];
                } 
                if ($think[$i]['id'] == $array['uploadbyid']) {
                    $res['uploadbyid'] = $think[$i]['realname'];
                } 
            }
        }
        $res['timelimit'] = $timelimit;
        $res['timeopen'] = date('Y-m-d H:i',$res['timeopen']);
        $res['timeclose'] = date('Y-m-d H:i',$res['timeclose']);
        return getBack(0, $res);
    }
    /**
     * @User:贾芸吉
     * @Date:18/7/26/15
     * @type:试卷拷贝
     */
    public function copyOut()
    {
        $res = $this->quizDetail();
        return $res;
    }

    /*
     * 根据试卷id得到试卷下的题列表
     * @author 冉孟圆
     */
    public function getQuizQuestionList () {
        $data = $this->data;

        $Question = model('ExamQuestion');

        $examClassroomQuizQuestions = new ExamClassroomQuizQuestions();
        $field = "cq.id,cq.pid,cq.name,cq.questiontext,cq.img as imgs,p.questionid,p.qtype,p.seq,p.score";
        $res_question = collection($examClassroomQuizQuestions->alias('p')
            ->join('__EXAM_CLASSROOM_QUIZ__ cc','cc.id = p.quiz')
            ->join('__EXAM_QUESTION__ cq','cq.id = p.questionid')
            ->where('p.quiz',$data['quiz'])
            ->where('cc.status',0)
            ->field($field)
            ->select())->toArray();
        if (!$res_question) {
            return getBack(1,'未查到相关数据');
        }


        for ($i = 0; $i < count($res_question); $i++){

            if ($res_question[$i]['qtype'] == 'match'|| $res_question[$i]['qtype'] == 'comprehensive'|| $res_question[$i]['qtype'] == 'readingcomprehension'){
                $id[] = $res_question[$i]['id'];//$id=〉有子问题的问题id
            }else{
                $no_ch_id[] = $res_question[$i]['id'];//$nu_ch_id->没有子问题id
            }
        }

        if (isset($id)){
            $ch_question = collection($Question->where('pid','in',$id)->where('status',0)->field('id, pid, qtype, difficulty, name, usepermise, questiontext, generalfeedback, timecreated, check, img as imgs')->order('timecreated', 'asc')->select())->toArray();//$ch_question——>匹配、综合、阅读理解的子问题
            //将子问题放在非子问题后面


            $res_question = array_merge($res_question, $ch_question);
            //将问题进行分类，取id
            for ($i = 0; $i < count($ch_question); $i++){
                $no_ch_id[] = $ch_question[$i]['id'];//$nu_ch_id->没有子问题id
            }
        }
        $answer = new ExamAnswer();
        $res_answer = $answer->getExAnswer($no_ch_id);
        //$res_answer->没有子题答案
        for ($i = 0; $i < count($res_question); $i++){
            //$k = 0;
            for ($j = 0; $j < count($res_answer); $j++){
                if ($res_question[$i]['id'] == $res_answer[$j]['questionid']){
                    switch ($res_question[$i]['qtype']){ 
                        //单选题
                        case 'singlechoice':
                        //多选题
                        case 'multiplechoice':
                            $res_question[$i]['answer'][] = [
                                'id'       => $res_answer[$j]['id'],
                                'name'     => $res_answer[$j]['answer'],
                                'fraction' => $res_answer[$j]['fraction'],
                                'tab'      => $res_answer[$j]['tab'],
                                'imgs'     => $res_answer[$j]['img']
                            ];
                            break;
                        //填空题
                        case 'shortanswer':
                            $arr_question = explode('||', $res_answer[$j]['answer']);
                            $res_question[$i]['answer'] = $arr_question;
                            break;
                        //判断题
                        case 'truefalse':
                        
                            $res_question[$i]['answer'][] = [
                                'answer'   => config('truefalse_con.' . $res_answer[$j]['answer']),
                                'fraction' => $res_answer[$j]['fraction']
                            ];
                            break;
                        //简答题
                        case 'essay':
                        //匹配题
                        case 'match':
                        //综合题
                        case 'comprehensive':
                            $res_question[$i]['answer'] = $res_answer[$j]['answer'];
                            break;
                    }
                }
            }
        }
        $res_question = $this->tree($res_question);
        return getBack(0, $res_question);
    }
    /*
     * 生成树
     * @author 冉孟圆
     */
    public function tree($data, $pid = ''){
        $temp = array();
        foreach ($data as $v) {
            if ($v['pid'] == $pid){
                $v['option'] = $this->tree($data, $v['id']);
                $temp[] = $v;
            }
        }
        return $temp;
     }
}