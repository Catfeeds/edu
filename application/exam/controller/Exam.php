<?php
namespace app\exam\controller;

use app\service\controller\Common;
use app\service\model\ClassroomUser;
use app\exam\model\ExamClassroomQuizAttempts;
use app\exam\model\ExamClassroomProject;
use app\exam\model\ExamClassroomQuestionAttemptStep;
use app\exam\model\ExamClassroomQuestionAttemptStepData;
use app\exam\model\ExamQuestion;
use app\exam\model\ExamClassroomProjectQuestions;
use app\exam\model\ExamClassroomQuiz;
use app\exam\model\ExamClassroomQuizQuestions;

class Exam extends Common
{
    public function __construct(){
        parent::__construct();
    }
    //试卷类
    protected $attemptStep = false;
    protected $quizAttemps = false;
    protected $examQuestion = false;
    /**
     * 开始测试
     * @author 宋玉琪 
     */
    public function startTest()
    {
        $this->examQuestion = new ExamQuestion();
        $this->quizAttemps = new ExamClassroomQuizAttempts();
        $this->attemptStep = new ExamClassroomQuestionAttemptStep();
        $quizQuestions = new ExamClassroomQuizQuestions();
        $data = $this->data;
        if((! isset($data['id'])) && empty($data['id']))
        return getBack(1, '参数错误！');
        $quizinfo = $this->isQuizAttemptsNormal($data['id']);
        if ($quizinfo['status'] != 0)
        return getBack(1, $quizinfo['data']);
        $quizinfo = $quizinfo['data'];
        $userinfo = $this->isJoinExam($quizinfo['classroomid'], $data['uid']);
        if ($userinfo == false)
        return getBack(1, '你不能参加此次考试');
        $ndata = [
            'project' => $data['id'],
            'userid' => $data['uid'],
            'layout' => ''
           ];
       $res = $this->quizAttemps->save($ndata);
       if (! $res)
       return getBack(1, '创建失败，请重试！');
       $attempsid = $this->quizAttemps->id;
       $quizQuestionsList = $quizQuestions->joinAnswerByQuizId($quizinfo['id']);
       if (is_null($quizQuestionsList))
       return getBack(1, '试卷信息出错！');
       $res = $this->saveQuestions($quizQuestionsList, $attempsid);
       if($res['status'] == 1)
       return getBack(1, $res['data']);
       return getBack(0, $res['data']);
    }
    /**
     * 开始考试
     * @author 宋玉琪 
     */
    public function start()
    {
        /* 身份验证及考试时间验证 */
        $this->examQuestion = new ExamQuestion();
        $this->quizAttemps = new ExamClassroomQuizAttempts();
        $this->attemptStep = new ExamClassroomQuestionAttemptStep();
        $projectQuestions = new ExamClassroomProjectQuestions();
        $data = $this->data;
        if(!isset($data['id']) || empty($data['id']))
        return getBack(1, '参数错误！');
        $projectinfo = $this->isAttemptsNormal($data['id']);//考试时间验证
        if ($projectinfo['status'] != 0)
        return getBack(1, $projectinfo['data']);
        $projectinfo = $projectinfo['data'];//考场信息
        $userinfo = $this->isJoinExam($projectinfo['classroom'], $data['uid']);//验证考试用户是否为课堂下的学生
        if (!$userinfo)
        return getBack(1, '登录用户不在该考场下！');
        /* 创建试卷 */
        $attempt = $this->quizAttemps->where(['status' => 0, 'userid' => $data['uid'], 'project' => $data['id']])->max('attempt');
        $ndata = [
             'project' => $data['id'],
             'userid' => $data['uid'],
             'layout' => '',
             'attempt' => $attempt + 1
            ];
        $res = $this->quizAttemps->save($ndata);
        if (!$res)
        return getBack(1, '创建失败，请重试！');
        $attempsid = $this->quizAttemps->id;
        $projectQuestionsList = $projectQuestions->joinAnswerByProjectId($projectinfo['id']);
        if (is_null($projectQuestionsList))
        return getBack(1, '试卷信息出错！');
        $res = $this->saveQuestions($projectQuestionsList, $attempsid);
        if($res['status'] == 1)
        return getBack(1, $res['data']);
        $attemptsData = $this->readQuestions($attempsid);
        if(empty($attemptsData))
        return getBack(0, '无题目');
        /* 对attempt_step_data答案的添加 */
        $res = $this->saveAttemptsStepData($attemptsData);
        if(!$res)
        return getBack(1, '创建失败！');
        return getBack(0, $attemptsData);
    }
    /**
     * 对attempt_step_data答案的添加
     * @param attemptsData 组成的试卷数据
     * @author 宋玉琪 
     */
    public function saveAttemptsStepData($attemptsData)
    {
        $stepData = new ExamClassroomQuestionAttemptStepData();
        $data = [];
        $numbers = [];
        $allowQtypes= ['singlechoice', 'multiplechoice', 'match'];
        $n = 0;
        foreach ($attemptsData as $item)
        {
            if(in_array($item['qtype'], $allowQtypes))
            {
                $numbers[] = ++$n;
            }
        }
        shuffle($numbers);
        foreach ($attemptsData as $item)
        {
            $arr = [
                'attemptstepid' => $item['id'],
                'qtype' => $item['qtype'],
                'answer' => ''
            ];
            $answer = '';
            if($item['qtype'] == 'singlechoice' || $item['qtype'] == 'multiplechoice')
            {
                foreach ($item['option'] as $val)
                {
                    $answer .= $val['id'] . ',';
                }
            }else if($item['qtype'] == 'match')
            {
                foreach ($item['option']['answer'] as $key => $val)
                {
                    $answer .= $key . ',';
                }
            }
            $answer = rtrim($answer, ',');
            $arr['answer'] = $answer;
            $arr['number'] = array_pop($numbers);
            $data[] = $arr;
        }
        $res = $stepData->saveAll($data);
        return $res;
    }
    /**
     * 添加试题并返回试卷
     * @author 宋玉琪 
     * @return attemptsData 试卷
     */
    public function saveQuestions($projectQuestionsList, $attempsid)
    {
        $questionsData = array();
        $questionsPid = array();
        $questionPidScore = array();
        foreach ($projectQuestionsList as $item)
        {
            if (isset($questionsData[$item['project_questions_questionid']]))
            {
                switch ($item['project_questions_qtype'])
                {
                    case 'singlechoice':
                    case 'multiplechoice':
                    case 'truefalse':
                    if ($item['answer_fraction'] == 100)
                    {
                        if (is_null($questionsData[$item['project_questions_questionid']]['answer']))
                        {
                            $questionsData[$item['project_questions_questionid']]['answer'] = $item['answer_id'];
                        }
                        else $questionsData[$item['project_questions_questionid']]['answer'] .= ',' . $item['answer_id'];
                    }
                }
                continue;
            }
            $arr = [
                'attempts' => $attempsid,
                'qtype' => $item['project_questions_qtype'],
                'score' => $item['project_questions_score'],
                'questionid' => $item['project_questions_questionid'],
                'sequencenumer' => $item['project_questions_sequencenumer'] + mt_rand(0, 100),
                'state' => 2,
                'answer' => null
            ];
            switch ($item['project_questions_qtype'])
            {
                case 'singlechoice'://单选
                case 'multiplechoice'://多选
                case 'truefalse'://判断
                    if ( $item['answer_fraction'] == 100)
                    {
                        $arr['answer'] =  $item['answer_id'];
                    }
                    break;
                case 'shortanswer'://填空
                case 'essay'://简答
                    $arr['answer'] =  $item['answer_answer'];
                    break;
                case 'match'://匹配
                case 'comprehensive'://综合
                case 'readingcomprehension'://阅读理解
                    $questionsPid[] = $item['project_questions_questionid'];
                    $questionPidScore[$item['project_questions_questionid']] = [
                        'pid' => $item['project_questions_questionid'],
                        'score' => $item['project_questions_score'],
                        'count' => 0
                    ];
                    break;
            }
            $questionsData[$item['project_questions_questionid']] = $arr;
        }
        $res = $this->attemptStep->saveAll($questionsData, false);
        if (!$res)
        return ['status' => 1, 'data' => '创建失败，请重试！'];
        /* 添加子题 */
        if(! empty($questionsPid))
        $res = $this->addChildrenQuestions($attempsid, $questionsPid, $questionPidScore);
        if (!$res)
        return ['status' => 1, 'data' => '创建失败，请重试！'];
        return ['status' => 0, 'data' => '创建完成！'];
    }
    /**
     * 存储子题目
     * @author 宋玉琪 
     * @param attempsid 试卷ID
     * @param questionsPid 具有子题的父题ID集合
     * @param questionPidScore 父题的总分及子题个数
     * @return res 存储结果
     */
    public function addChildrenQuestions($attempsid, $questionsPid, $questionPidScore)
    {
        $qtype = ['match', 'comprehensive', 'readingcomprehension'];
        $where = [
            'attempts' => $attempsid,
            'qtype' => ['in', $qtype]
        ];
        $Parentquestion = $this->attemptStep->where($where)->filed('id, questionid')->select();
        $questionsChildData = $this->examQuestion->joinAnswerInPid($questionsPid);
        $questionsChild = array();
        foreach ($questionsChildData as $item)
        {
            if (isset($questionsChild[$item['questions_id']]))
            {
                switch ($item['question_qtype'])
                {
                    case 'singlechoice':
                    case 'multiplechoice':
                    case 'truefalse':
                        if ($item['answer_fraction'] == 100)
                        {
                            if (is_null($questionsChild[$item['questions_id']]['answer']))
                            {
                                $questionsChild[$item['questions_id']]['answer'] = $item['answer_id'];
                            }
                            else $questionsChild[$item['questions_id']]['answer'] .= ',' . $item['answer_id'];
                        }

                }
                continue;
            }
            $arr = [
                'attempts' => $attempsid,
                'qtype' => $item['question_qtype'],
                'questionid' => $item['questions_id'],
                'pid' => $item['questions_pid'],
                'sequencenumer' => $item['questions_sequencenumer'],
                'state' => 2,
                'answer' => null
            ];
            switch ($item['question_qtype'])
            {
                case 'singlechoice'://单选
                case 'multiplechoice'://多选
                case 'truefalse'://判断
                case 'match'://匹配
                    if ($item['answer_fraction'] == 100)
                    {
                        $arr['answer'] =  $item['answer_id'];
                    }
                    break;
                case 'shortanswer'://填空
                case 'essay'://简答
                case 'comprehensive'://综合
                case 'readingcomprehension'://阅读理解
                    $arr['answer'] =  $item['answer_answer'];
                    break;
            }
            $questionsChild[$item['questions_id']] = $arr;
        }
        /* 计算某题的子题个数 */
        foreach ($questionsChild as $item)
        {
            if (isset($questionPidScore[$item['pid']]))
            ++$questionPidScore[$item['pid']]['count'];
        }
        /* 添加子题的Score字段属性值,并覆盖pid */
        foreach ($questionsChild as $key => $item)
        {
            $sumscore = $questionPidScore[$item['pid']]['score'];
            $count = $questionPidScore[$item['pid']]['count'];
            $questionsChild[$key]['score'] = $sumscore / $count;
            foreach ($Parentquestion as $val)
            {
                if ($val['questionid'] == $item['pid'])
                {
                    $questionsChild[$key]['pid'] = $val['id'];
                    break;
                }
            }
        }
        $res = $this->attemptStep->saveAll($questionsChild, false);
        return $res;
    }
    /**
     * 读取试卷中的题目
     * @author 宋玉琪 
     * @param attempsid 试卷ID
     * @return attemptsData 整理好格式的试卷
     */
    public function readQuestions($attempsid)
    {
        $questionData = $this->attemptStep->joinAnswerByAttempId($attempsid);
        $attemptsData = array();
        /* 整合答案 */
        foreach ($questionData as $item)
        {
            if (isset($attemptsData[$item['step_id']]))
            {
                $arr = [
                    'id' => $item['answer_id'],
                    'answer' => $item['answer_answer'],
                    'tab' => $item['answer_tab'],
                    'img' => $item['answer_img'],
                ];
                $attemptsData[$item['step_id']]['option'][] = $arr;
                continue;
            }
            $arr = [
                'id' => $item['step_id'],
                'sequencenumer' => $item['step_sequencenumer'],
                'pid' => $item['step_pid'],
                'score' => $item['step_score'],
                'qtype' => $item['question_type'],
                'questiontext' => $item['question_questiontext'],
                'img' => $item['question_img'],
                'option' => [
                    [
                        'id' => $item['answer_id'],
                        'answer' => $item['answer_answer'],
                        'tab' => $item['answer_tab'],
                        'img' => $item['answer_img'],
                    ]
                ]
            ];
            $attemptsData[$item['step_id']] = $arr;
        }
        /* 分出三种类型的题 有子题的父题 子题 和没有子题的 */
        $attemptsDataParent = array();
        $attemptsDataChildren = array();
        $attemptsDataDirect = array();
        $attemptsDataParentType = ['match', 'comprehensive', 'readingcomprehension'];
        foreach ($attemptsData as $item)
        {
            if ( in_array($item['qtype'], $attemptsDataParentType) && ($item['pid'] == '0') )
            {
                $item['children'] = array();
                $attemptsDataParent[] = $item;
            }
            else if ($item['pid'] != '0' )
            $attemptsDataChildren[] = $item;
            else $attemptsDataDirect[] = $item;
        }
        /* 向有子题的父题中插入相应子题  */
        foreach ($attemptsDataParent as $key => $val)
        {
            foreach ($attemptsDataChildren as $item)
            {
                if ($item['pid'] == $val['id'])
                {
                    $attemptsDataParent[$key]['children'][] = $item;
                }
            }
        }
        /* 组合试题 */
        $attemptsData = array();
        $baseData = array_merge($attemptsDataDirect, $attemptsDataParent);
        foreach ($baseData as $item)
        {
            $arr = array();
            switch ($item['qtype'])
            {
                case 'singlechoice':
                case 'multiplechoice':
                    $arr = $item;
                    break;
                case 'truefalse':
                    $arr = $this->readtruefalse($item);//判断题    
                    break;
                case 'shortanswer':
                case 'essay':
                    $arr = $this->readessayorshort($item);//简答题   
                    break;
                case 'match':
                    $arr = $this->readmatch($item);//匹配题    
                    break;
                case 'comprehensive':
                    $arr = $this->readcomprehensive($item);//综合题    
                    break;
                case 'readingcomprehension':
                    $arr = $this->readreadingcomprehension($item);//阅读理解
                    break;
            }
            $attemptsData[] = $arr;
        }
        return $attemptsData;
    }
    /**
     * 组织简答题或者填空题格式
     * @author 宋玉琪 
     * @param data 一道题的数据
     * @return data 规定格式的一道题
     */
    public function readessayorshort($data)
    {
        unset($data['option']);
        return $data;
    }
    /**
     * 组织判断题格式
     * @author 宋玉琪 
     * @param data 一道题的数据
     * @return data 规定格式的一道题
     */
    public function readtruefalse($data)
    {
        $option = array();
        foreach ($data['option'] as $item)
        {
            $option[] = $item['id'];
        }
        $data['option'] = $option;
        return $data;
    }
        /**
     * 组织匹配题格式
     * @author 宋玉琪 
     * @param data 一道题的数据
     * @return data 规定格式的一道题
     */
    public function readmatch($data)
    {
        $data['option'] = [
            'question' => [],
            'answer' => []
        ];
        foreach ($data['children'] as $item)
        {
            $data['option']['question'][$item['id']] = $item['questiontext'];
            $data['option']['answer'][$item['option'][0]['id']] = $item['option'][0]['answer'];
        }
        unset($data['children']);
        return $data;
    }
        /**
     * 组织综合题格式
     * @author 宋玉琪 
     * @param data 一道题的数据
     * @return data 规定格式的一道题
     */
    public function readcomprehensive($data)
    {
        $data['option'] = [
            'question' => []
        ];
        foreach ($data['children'] as $item)
        {
            $data['option']['question'][$item['id']] = $item['questiontext'];
        }
        unset($data['children']);
        return $data;
    }
    /**
     * 组织阅读理解格式
     * @author 宋玉琪 
     * @param data 一道题的数据
     * @return data 规定格式的一道题
     */
    public function readreadingcomprehension($data)
    {
        $data['option'] = array();
        foreach ($data['children'] as $item)
        {
            $arr = array();
            switch ($item['qtype'])
            {
                case 'singlechoice':
                case 'multiplechoice':
                    $arr = $item;
                    break;
                case 'truefalse':
                    $arr = $this->readtruefalse($item);//判断题    
                    break;
                case 'shortanswer':
                case 'essay':
                    $arr = $this->readessayorshort($item);//简答题   
                    break;
            }
            $data['option'][] = $arr;
        }
        unset($data['children']);
        return $data;
    }
    /**
     * 判断试卷是否正常
     * @author 宋玉琪 
     * @return status:1 还没有到考试时间
     * @return status:0 正常
     * @return data 查询到的project信息
     */
    public function isAttemptsNormal($protectid)
    {
        $project = new ExamClassroomProject();
        $where = [
            'id' => $protectid,
            'status' => '0'
        ];
        $projectinfo = $project->where($where)->find();
        if (!$projectinfo) {
            echo getBack(1, '该堂考试不存在');exit;
        }
        if (time() < $projectinfo->getData('starttime'))
        return ['status' => 1, 'data' => '考试时间未到！', 'surplus' => -1];
        if (time() > $projectinfo->getData('endtime'))
        return ['status' => 1, 'data' => '考试时间已过！', 'surplus' => 1];
        return ['status' => 0, 'data' => $projectinfo, 'surplus' => 0];
    }
    /**
     * 判断试卷是否正常
     * @author 宋玉琪 
     * @return status:1 还没有到考试时间
     * @return status:0 正常
     * @return data 查询到的project信息
     */
    public function isQuizAttemptsNormal($quizid)
    {
        $quiz = new ExamClassroomQuiz();
        $where = [
            'id' => $quizid,
            'status' => '0'
        ];
        $quizinfo = $quiz->where($where)->find();
        if (! $quizinfo) {
            echo getBack(1, '该堂考试不存在');exit;
        }
        if (time() < strtotime($quizinfo['timeopen']))
        return ['status' => 1, 'data' => '考试时间未到！', 'surplus' => -1];
        if (time() > strtotime($quizinfo['timeclose']))
        return ['status' => 1, 'data' => '考试时间已过！', 'surplus' => 1];
        return ['status' => 0, 'data' => $quizinfo, 'surplus' => 0];
    }
    /**
     * 判断试卷是否正常
     * @author 宋玉琪 
     * @param projectinfo 还没有到考试时间
     * @param uid 当前用户的uid
     * @return userinfo 查询到的用户信息
     */
    public function isJoinExam($classroom, $uid)
    {
        $user = new ClassroomUser();
        $where = [
            'classroom_id' => $classroom,
            'user_id' => $uid,
            'role' => 1
        ];
        $userinfo = $user->where($where)->find();
        return !$userinfo ? false : true;
    }
    /**
     * 保存答案
     *@author 李桐
     *@param varchar(100) qtype [description]
     */
    public function saveAnswer(){
        $data = $this->data;
        $this->attemptStep = new ExamClassroomQuestionAttemptStep();
        $this->quizAttemps = new ExamClassroomQuizAttempts();
        $project = $this->quizAttemps->where('id', $data['attempts'])->where('status', 0)->find();
        if (!$project || $project->finished == 1){
            $message = [
                'state'   => 1,
                'msg'     => '试卷已提交',
                'surplus' => 0
            ];
            return getBack(1, $message);
        }
        $isNormal = $this->isAttemptsNormal($project->project);
        if (!$isNormal['status']){
            //判断剩余时间
            $surplus = $this->remainingTime($project->timestart, $isNormal['data']->getData('endtime'), $isNormal['data']->limittime, $data['attempts']);
            switch ($data['qtype']){
                case 'singlechoice'://单选题
                case 'multiplechoice'://多选题
                    $res = $this->saveChoice($data);
                    break;
                case 'shortanswer'://填空题
                    $res = $this->saveShortanswer($data);
                    break;
                case 'truefalse'://判断题
                    $res = $this->saveTruefalse($data);
                    break;
                case 'match'://匹配题
                    $res = $this->saveMatch($data);
                    break;
                case 'essay'://简答题
                    $res = $this->saveEssay($data);
                    break;
                case 'comprehensive'://综合题
                    $res = $this->saveComprehensive($data);
                    break;
                case 'readingcomprehension'://阅读理解
                    $res = $this->saveReadingcomprehension($data);
                    break;
            }
            $message = [
                'state'   => $res ? 0:1,
                'msg'     => $res ? '保存成功':'保存失败',
                'surplus' => $surplus
            ];
            return getBack($res ? 0:1, $message);
        }else{
            $message = [
                'state'   => 1,
                'msg'     => $isNormal['data'],
                'surplus' => 0
            ];
            return getBack(1, $message);
        }
    }
    /**
     *方法详情
     *保存选择题答案(需要算分)
     * @author 李桐
     * @param [type] $[name] [description]
     */
    public function saveChoice($data){
        $res = $this->attemptStep->where('id', $data['id'])->find();
        $answer = explode(',', $res['answer']);
        $true = 0;//正确答案个数
        for ($i = 0; $i < count($data['answer']); $i++){
            if (!in_array($data['answer'][$i], $answer)){
                $true = 0;
                break;
            } else {
                $true++;
            }    
        }
        if ($true == 0){
            $grade = 0;
        }else if ($true == count($answer)){
            $grade = $res['score'];
        }else if ($true > 0 && $true < count($answer)){
            $grade = $res['score'] / 2;
        }
        $result = $this->attemptStep->save([
            'grade'      => $grade,
            'stu_answer' => implode(',', $data['answer']),
            'state'      => 1
            ], ['id' => $data['id']]);
        return $result;
    }
    /**
     *方法详情
     *保存填空题答案
     * @author 李桐
     * @param [type] $[name] [description]
     */
    public function saveShortanswer($data){
        $res = $this->attemptStep->where('id', $data['id'])->find();
        $answer = explode('||', $res['answer']);
        if (count($data['answer']) >= count($answer)){
            $is_complete = 1;//完成
        }else{
            $is_complete = 2;//未完成
        }
        $stu_answer = implode('||', $data['answer']);
        $result = $this->attemptStep->save([
            'stu_answer' => $stu_answer,
            'state'      => $is_complete
            ], ['id' => $data['id']]);
        return $result;
    }
    /**
     * 方法详情
     * 保存判断题答案(需要算分)
     * @author 李桐 
     * 
     */
    public function saveTruefalse($data){
        $res = $this->attemptStep->where('id', $data['id'])->find();
        if ($data['answer'] == $res['answer']){
            $grade = $res['score'];
        }else{
            $grade = 0;
        }
        $result = $this->attemptStep->save([
            'grade'      => $grade,
            'stu_answer' => $data['answer'],
            'state'      => 1
            ], ['id' => $data['id']]);
        return $result;
    }
    /**
     * 方法详情
     * 保存匹配题答案(需要算分)
     * @author 李桐
     * @param [type] $[name] [description]
     */
    public function saveMatch($data){
        $res = $this->attemptStep->where('id', $data['id'])->find();
        if ($data['answer'] == $res['answer']){
            $grade = $res['score'];
        }else{
            $grade = 0;
        }
        $this->attemptStep->save([
                'stu_answer' => $data['answer'],
                'grade'      => $grade,
                'state'      => 1
            ], ['id' => $data['id']]);
        $ch_question = collection($this->attemptStep->where('pid', $data['pid'])->select())->toArray();
        $state = 1;
        $grade = 0;
        for ($i = 0; $i < count($ch_question); $i++){
            $grade += $ch_question[$i]['grade'];
            if($ch_question[$i]['state'] == 2){
                $state = 2;
            }
        }
        $result = $this->attemptStep->where('id', $data['pid'])->update([
                'state' => $state,
                'grade' => $grade
            ]);
        return $result;
    }
    /**
     * 方法详情
     * 保存简答题答案
     * @author 李桐
     * @param [type] $[name] [description]
     */
    public function saveEssay($data){
        $result = $this->attemptStep->save([
            'stu_answer' => $data['answer'],
            'state'      => 1
            ], ['id' => $data['id']]);
        return $result;
    }
    /**
     * 方法详情
     * 保存综合题答案
     * @author 李桐
     * @param [type] $[name] [description]
     */
    public function saveComprehensive($data){
        $result = $this->saveEssay($data);
        $ch_question = $this->attemptStep->where('pid', $data['pid'])->where('state', 2)->find();
        if (!$ch_question){
            $result = $this->attemptStep->save(['state' => $state], ['id' => $data['pid']]);
        }
        return $result;
    }
    /**
     * 方法详情
     * 保存阅读理解题答案
     * @author 李桐
     * @param [type] $[name] [description]
     */
    public function saveReadingcomprehension($data){
        switch ($data['option']['qtype']){
            case 'singlechoice'://单选题
            case 'multiplechoice'://多选题
                $res = $this->saveChoice($data['option']);
                break;
            case 'shortanswer'://填空题
                $res = $this->saveShortanswer($data['option']);
                break;
            case 'truefalse'://判断题
                $res = $this->saveTruefalse($data['option']);
                break;
            case 'Essay'://简答题
                $res = $this->saveEssay($data['option']);
                break;
        }
        $ch_question = collection($this->attemptStep->where('pid', $data['pid'])->select())->toArray();
        $grade = 0;
        $state = 1;
        for ($i = 0; $i < count($ch_question); $i++){
            if ($ch_question[$i]['grade'] != -1){
                $grade += $ch_question[$i]['grade'];
            }
            if ($ch_question[$i]['state'] == 2){
                $state = 2;
            }
        }
        $result = $this->attemptStep->save([
                'grade' => $grade,
                'state' => $state
            ], ['id' => $data['pid']]);
        return $result;
    }
    /**
     *方法详情
     *计算并返回剩余时间，如果考试结束则提交试卷
     * @author 李桐
     * @param int $timestart 表ExamClassroomQuizAttempts中的timestart
     * @param int $endtime   表ExamClassroomProject中的endtime
     * @param int $limittime 表ExamClassroomProject中的limittime
     * @param char(32) $id   试卷id
     */
    public function remainingTime($timestart, $endtime, $limittime, $id){
        $time_now  = time();//现在的时间
        $usedtime  = $time_now - $timestart;
        $surplus   = $limittime - $usedtime;
        if ($surplus <= 0 || $time_now > $endtime){
            $this->forceSubmit($id);
            $message = [
                'state'   => 1,
                'msg'     => '考试已结束',
                'surplus' => 0
            ];
            echo getBack(1, $message);
            exit;
        }
        return $surplus;
    }
    /**
     * 方法详情
     * 提交试卷并计算总分
     * @author 李桐
     * @param char(32) id 试卷id
     */
    public function forceSubmit($attempts){
        $pid = '0';
        $time = time();
        $res = collection($this->attemptStep->where('attempts', $attempts)->where('pid', $pid)->select())->toArray();
        $score = 0; 
        for ($i = 0; $i < count($res); $i++){
            if ($res[$i]['grade'] != -1){
                $score += $res[$i]['grade'];
            }
        }
        $res_quiz_attempts = $this->quizAttemps->where('id', $attempts)->where('status', 0)->find();
        $usedtime = $time - $res_quiz_attempts->timestart;
        $result = $this->quizAttemps->save([
                'sumgrades'    => $score,
                'timefinish'   => $time,
                'usedtime'     => $usedtime,
                'timemodified' => $time,
                'finished'     => 1
            ], ['id' => $attempts]);
    }
    /**
     *方法详情
     *考试结束（提交卷子）
     * @author 李桐
     * @param [type] $[name] [description]
     */
    public function subAttempt(){
        $data = $this->data;
        $this->attemptStep = new ExamClassroomQuestionAttemptStep();
        $this->quizAttemps = new ExamClassroomQuizAttempts();
        $project = $this->quizAttemps->where('id', $data['id'])->where('status', 0)->find();
        if (!$project || $project->finished == 1){
            $message = [
                'state'   => 1,
                'msg'     => '试卷已提交',
                'surplus' => 0
            ];
            return getBack(1, $message);
        }
        $isNormal = $this->isAttemptsNormal($project->project);
        if (!$isNormal['status']){
            //剩余时间验证
            $this->remainingTime($project->timestart, $isNormal['data']->getData('endtime'), $isNormal['data']->limittime, $data['id']);
            foreach ($data['answer'] as $key => $value) {
                switch ($key) {
                    case 'singlechoice'://单选题
                    case 'multiplechoice'://多选题
                        $res = $this->updataChoice($value);
                        break;
                    case 'shortanswer'://填空题
                        $res = $this->updataShortanswer($value);
                        break;
                    case 'truefalse'://判断题
                        $res = $this->updataTruefalse($value);
                        break;
                    case 'match'://匹配题
                        $res = $this->updataMatch($value);
                        break;
                    case 'essay'://简答题
                    case 'comprehensive'://综合题
                        $res = $this->updataComprehensiveAndEssay($value);
                        break;
                    case 'readingcomprehension'://阅读理解
                        $res = $this->updataReadingcomprehension($value);
                        break;
                }
            }
            return getBack(0, "保存成功！");
        }else {
            if($isNormal['surplus'] == -1){
                $this->quizAttemps->save(['status' => 1], ['id' => $project->id]);
            }else if ($isNormal['surplus'] == 1) {
                $this->forceSubmit($data['id']);
            }
            $message = [
                    'state'   => 1,
                    'msg'     => $isNormal['data'],
                    'surplus' => 0
                ];
            return getBack(1, $message);
        }
    }
    /**
     *方法详情
     *保存所有选择题答案(需要算分)
     *@author 李桐
     *@param [type] $[name] [description]
     */
    public function updataChoice($value){
        //取出选择题id
        for ($i = 0; $i < count($value); $i++){
            $id[] = $value[$i]['id'];
        }
        //根据选择题id查出数据库里的信息
        $database_res = collection($this->attemptStep->where('id', 'in', $id)->select())->toArray();
        if (!$database_res){
            echo getBack(1, "保存失败！");
            exit;
        }
        //将数据库中的标准答案分解成数组并取出pid
        for ($i = 0; $i < count($database_res); $i++){
            $database_res[$i]['answer'] = explode(',', $database_res[$i]['answer']);
        }
        //计算该题得分，并将相关信息放入list中
        for ($i = 0; $i < count($value); $i++){
            for ($j = 0; $j < count($database_res); $j++){
                if ($value[$i]['id'] == $database_res[$j]['id']){
                    $true = 0;
                    //计算出多选题正确答案个数
                    for ($k = 0; $k < count($value[$i]['answer']); $k++){
                        if (!in_array($value[$i]['answer'][$k], $database_res[$j]['answer'])){
                            $true = 0;
                            break;
                        } else {
                            $true++;
                        }    
                    }
                    if ($true == 0){
                        $grade = 0;
                    }else if ($true == count($database_res[$j]['answer'])){
                        $grade = $database_res[$j]['score'];
                    }else if ($true > 0 && $true < count($database_res[$j]['answer'])){
                        $grade = $database_res[$j]['score'] / 2;
                    }
                    $list[] = [
                        'id'         => $value[$i]['id'],
                        'stu_answer' => implode(',', $value[$i]['answer']),
                        'grade'      => $grade
                    ];
                }
            }
        }
        $this->attemptStep->saveAll($list);
    }
    /**
     *方法详情
     * 保存所有填空题答案
     * @author 李桐
     * @param [type] $[name] [description]
     */
    public function updataShortanswer($value){
        for ($i = 0; $i < count($value); $i++){
            $list[] = [
                'id'         => $value[$i]['id'], 
                'stu_answer' => implode('||', $value[$i]['answer'])
            ];
        }
        $this->attemptStep->saveAll($list);
    }
    /**
     * 方法详情
     * 保存所有判断题答案（需要算分）
     * @author 李桐
     * @param [type] $[name] [description]
     */
    public function updataTruefalse($value){
        for ($i = 0; $i < count($value); $i++){
            $id[] = $value[$i]['id'];
         }
         $database_res = collection($this->attemptStep->where('id', 'in', $id)->select())->toArray();
        if (!$database_res){
            echo getBack(1, "保存失败！");
            exit;
        }
         for ($i = 0; $i < count($value); $i++){
            for ($j = 0; $j < count($database_res); $j++){
                if ($value[$i]['id'] == $database_res[$j]['id']){
                    $grade = 0;
                    if ($value[$i]['answer'] == $database_res[$j]['answer']){
                        $grade = $database_res[$j]['score'];
                    }
                    $list[] = [
                        'id'         => $value[$i]['id'], 
                        'stu_answer' => $value[$i]['answer'], 
                        'grade'      => $grade
                    ];
                }
            }
        }
        $this->attemptStep->saveAll($list);
    }
    /**
     * 方法详情
     * 保存所有匹配题答案(需要算分)
     * @author 李桐 <[email address]>
     */
    public function updataMatch($value){
        foreach ($value as $k1 => $v1) {
            foreach ($v1 as $v2) {
                $id[] = $v2['id'];
                $answer[] = $v2['answer'];
            }
        }
        $database_res = collection($this->attemptStep->where('id', 'in', $id)->select())->toArray();
        if (!$database_res){
            echo getBack(1, "保存失败！");
            exit;
        }
        //计算子问题答案，并组合子问题数据到list中
        for ($i = 0; $i < count($id); $i++){
            for ($j = 0; $j < count($database_res); $j++){
                $pid[$database_res[$j]['pid']] = '';//获取pid
                if ($id[$i] == $database_res[$j]['id']){
                    $grade = 0;
                    if ($answer[$i] == $database_res[$j]['answer']){
                        $grade = $database_res[$j]['score'];
                    }
                    $list[] = [
                        'id'         => $id[$i],
                        'grade'      => $grade,
                        'stu_answer' => $answer[$i],
                        'pid'        => $database_res[$j]['pid']
                    ];
                }
            }
        }
        $pid = array_keys($pid);
        $database_pid = collection($this->attemptStep->where('id', 'in', $pid)->select())->toArray();
        if (!$database_pid){
            echo getBack(1, "保存失败！");
            exit;
        }
        for ($i = 0; $i < count($database_pid); $i++){
            $grade = 0;
            for ($j = 0; $j < count($list); $j++){
                if ($database_pid[$i]['id'] == $list[$j]['pid']){
                    $grade += $list[$j]['grade'];
                }
            }
            $list_pid[] = [
                'id'    => $database_pid[$i]['id'],
                'grade' => $grade
            ];
        }
        $list = array_merge($list, $list_pid);
        $this->attemptStep->saveAll($list);
    }
    /**
     * 方法详情
     * 保存所有综合题和简答题答案
     * @author 李桐 <[email address]>
     */
    public function updataComprehensiveAndEssay($value){
        for ($i = 0; $i < count($value); $i++){
            $list[] = [
                'id'         => $value[$i]['id'],
                'stu_answer' => $value[$i]['answer']
            ];
        }
        $this->attemptStep->saveAll($list);
    }
    /**
     * 方法详情
     * 保存所有阅读理解答案
     * @author 李桐 <[email address]>
     */
    public function updataReadingcomprehension($value){
        for ($i = 0; $i < count($value); $i++){
            switch ($value[$i]['qtype']) {
                case 'singlechoice'://单选题
                case 'multiplechoice'://多选题
                    $data['choice'][] = $value[$i];
                    break;
                case 'shortanswer'://填空题
                    $data['shortanswer'][] = $value[$i];
                    break;
                case 'truefalse'://判断题
                    $data['truefalse'][] = $value[$i];
                    break;
                case 'essay'://简答题
                    $data['essay'][] = $value[$i];
                    break;
            }
            $id[] = $value[$i]['id'];
        }
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'choice'://多选题
                    $res = $this->updataChoice($value);
                    break;
                case 'shortanswer'://填空题
                    $res = $this->updataShortanswer($value);
                    break;
                case 'truefalse'://判断题
                    $res = $this->updataTruefalse($value);
                    break;
                case 'essay'://简答题
                    $res = $this->updataComprehensiveAndEssay($value);
                    break;
            }
        }
        $database_res = collection($this->attemptStep->where('id', 'in', $id)->select())->toArray();
        if (!$database_res){
            echo getBack(1, "保存失败！");
            exit;
        }
        for ($i = 0; $i < count($database_res); $i++){
            $pid[$database_res[$i]['pid']] = '';
        }
        $pid = array_keys($pid);
        $database_pid = collection($this->attemptStep->where('id', 'in', $pid)->select())->toArray();
        if (!$database_pid){
            echo getBack(1, "保存失败！");
            exit;
        }
        for ($i = 0; $i < count($database_pid); $i++){
            $grade = 0;
            for ($j = 0; $j < count($database_res); $j++){
                if ($database_pid[$i]['id'] == $database_res[$j]['pid']){
                    if ($database_res[$j]['grade'] != -1){
                        $grade += $database_res[$j]['grade'];
                    }
                }
            }
            $list[] = [
                'id'    => $database_pid[$i]['id'],
                'grade' => $grade
            ];
        }
        $this->attemptStep->saveAll($list);
    }
}