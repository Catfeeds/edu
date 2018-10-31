<?php
namespace app\exam\controller;

use app\service\controller\Common;
use app\exam\model\ExamClassroomQuizAttempts;
use app\service\model\ClassroomUser;

class AnswerSheet extends Common
{
    public function __construct(){
        parent::__construct();
    }
    /*
     * 答卷批改主观题
     * author jyj
     * time 18/08/06/10
     * [
     *      'id'     => 'exam_classroom_question_attempt_step表id',
     *      'score'  => '该题得分',
     *      'remark' => '老师批语',
     * ]
     */
    public function correctSubProblem(){
         $data = $this->data;
         $where['id'] = $data['id'];
         unset($data['id']);
         $result = Model('ExamClassroomQuestionAttemptStep')->UpdateMark($where, $data);
         if ($result['status'] !== false){
            return getBack(0, '操作成功');
         }else{
            return getBack(1, '操作失败，' . $result['error_message']);

         }
    }
    /**
     * 答卷列表
     * @author 陈博文 
     * @return array 返回的组合后的数组
     */
    public function index() {
        $data = $this->data;
        $ClassroomUser = new ClassroomUser();
        $ExamClassroomQuizAttempts = new ExamClassroomQuizAttempts();
        $configPage['list_rows'] = config('paginate.list_rows');
        isset($data['row']) && $data['row'] > 0 && $configPage['list_rows'] = $data['row'];
        $configPage[config('paginate.var_page')] = 1;
        isset($data['page']) && $data['page'] > 0 && $configPage[config('paginate.var_page')] = $data['page'];
        if (isset($data['classroom']) && $data['classroom'] != null) {
            $role = $ClassroomUser->where(['user_id' => $data['uid'], 'classroom_id' => $data['classroom']])->value('role');
            $join = [
                ['__EXAM_CLASSROOM_PROJECT__ project', 'project.id = attempts.project'],
                ['__EXAM_CLASSROOM_QUIZ__ quiz', 'quiz.classroomid = project.classroom'],
                ['__CLASSROOM__ classroom', 'classroom.id = quiz.classroomid'],
            ];
            if (isset($data['type']) && $data['type'] != null) {
                switch ($data['type']) {
                    case 0:
                    ($role == 1) ? $where['attempts.userid'] = $data['uid'] : null;
                        break;
                    case 1:
                    if ($role == 1) {
                        $where['attempts.userid'] = $data['uid'];
                        $where['quiz.preferredbehaviour'] = 0;
                    }
                        break;
                }
            } else {
                if ($role == 1) {
                    $where['attempts.userid'] = $data['uid'];
                    $where['quiz.preferredbehaviour'] = 0;
                }
            }
            isset($data['name']) ? $where['project.name'] = $data['name']: null;
            $where = ['project.status' => 0, 'quiz.status' => 0, 'attempts.status' => 0];
            $field = 'quiz.name as quiz_name, project.name as pro_name, quiz.id as quiz_id, project.id as pro_id, classroom.fullname, classroom.shortname, attempts.status, quiz.examtype, project.starttime, attempts.finished';
            $query = $ExamClassroomQuizAttempts->alias('attempts')
            ->join($join)
            ->where('project.starttime', '<=', time())
            ->where($where)
            ->field($field)
            ->order('project.starttime desc')
            ->paginate($configPage, config('paginate.custom'))->toArray();
            $query_data = $query['data'];
            foreach ($query_data as $k => $v) {
                $new[$k] = $v;
                if ($v['examtype'] == 0) {
                    unset($new[$k]['pro_name']);
                    unset($new[$k]['pro_id']);
                    $new[$k]['name'] = $v['quiz_name'];
                    $new[$k]['id'] = $v['quiz_id'];
                    unset($new[$k]['quiz_name']);
                    unset($new[$k]['quiz_id']);
                }
                if ($v['examtype'] == 1) {
                    unset($new[$k]['quiz_name']);
                    unset($new[$k]['quiz_id']);
                    $new[$k]['name'] = $v['pro_name'];
                    $new[$k]['id'] = $v['pro_id'];
                    unset($new[$k]['pro_name']);
                    unset($new[$k]['pro_id']);
                }
                $new[$k]['timeopen'] = date('Y/m/d H:i-Y/m/d H:i',$v['starttime']);
                $new[$k]['type'] = $v['examtype'];
                unset($new[$k]['starttime']);
                unset($new[$k]['examtype']);
            }
            //单独拿出去掉finished
            foreach ($new as $k => $v) {
                if ($v['finished'] == 1) {
                    $finished[] = $v;
                    unset($finished[$k]['finished']);
                }
            }
            foreach ($new as $k => $v) {
                unset($new[$k]['finished']);
            }
            //二维数组去重数据
            $unique_array = $this->array_unique_2D($new);
            //总人数数量
            for ($i = 0; $i < count($unique_array); $i++) {
                $joinNum = 0;
                $finishedNum = 0;
                for ($j = 0; $j < count($new); $j++) {
                    if (!array_diff_assoc($unique_array[$i], $new[$j])) {
                        $joinNum++;
                    }
                }
                for ($j = 0; $j < count($finished); $j++) {
                    if (!array_diff_assoc($unique_array[$i], $finished[$j])) {
                        $finishedNum++;
                    }
                }
                $unique_array[$i]['joinnum'] = $joinNum;
                $unique_array[$i]['alreadysum'] = $finishedNum;
            }
            unset($query['data']);
            $page_info = $query;
            $return_data = [$page_info, $unique_array];
            if ($return_data) {
                return getBack(0, $return_data);
            } else {
                return getBack(1, '数据错误');
            }
        }

    }
    /**
     * 二维数组去重复值
     * @author 陈博文
     * @param  array  $array 二维数组
     * @return array  $out   去重后新数组
     */
    public function array_unique_2D($array) {
        $out = array();
        foreach ($array as $key => $value) {
            if (!in_array($value, $out)){
                $out[$key] = $value;
            }
        }
        $out = array_values($out);
        return $out;
    }
    /**
     * 答卷详情
     * @author 陈博文
     * @return array  $arr   组合后格式数组
     */
    public function details() {
        $data = $this->data;
        $ExamClassroomQuizAttempts = new ExamClassroomQuizAttempts();
        $join = [
            ['__USER__ user', 'user.id = attempts.userid'],
            ['__EXAM_CLASSROOM_PROJECT__ project','project.id = attempts.project'],
            ['__EXAM_CLASSROOM_QUESTION_ATTEMPT_STEP__ que_atmp_step', 'que_atmp_step.attempts = project.quiz'],
            ['__EXAM_CLASSROOM_QUESTION_ATTEMPT_STEP_DATA__ que_atmp_data', 'que_atmp_data.attemptstepid = que_atmp_step.id'],
            ['__EXAM_ANSWER__ answer', 'answer.id = que_atmp_data.answer'],
            ['__EXAM_QUESTION__ question', 'question.id = answer.questionid']
        ];
        $field = 'user.realname, attempts.attempt, attempts.sumgrades, attempts.timestart, attempts.timefinish, attempts.usedtime, que_atmp_step.sequencenumer, que_atmp_step.id as step_id, que_atmp_step.score, que_atmp_data.score as grade, question.qtype, question.questiontext, question.img as question_img, question.generalfeedback, que_atmp_data.remarks, que_atmp_data.id as data_id, answer.answer, answer.tab, answer.img as answer_img, answer.fraction';
        $where = ['answer.status' => 0, 'question.status' => 0, 'attempts.status' => 0, 'user.status' => 0, 'attempts.id' => $data['attempt']];
        $query = collection($ExamClassroomQuizAttempts->alias('attempts')->join($join)->field($field)->where($where)->select())->toArray();
        foreach ($query as $k => $v) {
            $combination['username'] = $v['realname'];
            $combination['attempt'] = $v['attempt'];
            $combination['sumgrades'] = $v['sumgrades'];
            $combination['timestart'] = date('Y/m/d H:i:s', $v['timestart']);
            $combination['timefinish'] = date('Y/m/d H:i:s', $v['timefinish']);
            $combination['usedtime'] = ($v['usedtime'] / 60);
            switch ($v['qtype']) {
                case 'singlechoice':
                case 'multiplechoice':
                    $choice[$v['qtype']][$v['sequencenumer']]['id'] = $v['step_id'];
                    $choice[$v['qtype']][$v['sequencenumer']]['sequencenumer'] = $v['sequencenumer'];
                    $choice[$v['qtype']][$v['sequencenumer']]['score'] = $v['score'];
                    $choice[$v['qtype']][$v['sequencenumer']]['grade'] = $v['grade'];
                    $choice[$v['qtype']][$v['sequencenumer']]['qtype'] = $v['qtype'];
                    $choice[$v['qtype']][$v['sequencenumer']]['questiontext'] = $v['questiontext'];
                    $choice[$v['qtype']][$v['sequencenumer']]['img'] = $v['question_img'];
                    $choice[$v['qtype']][$v['sequencenumer']]['generalfeedback'] = $v['generalfeedback'];
                    $choice[$v['qtype']][$v['sequencenumer']]['remarks'] = $v['remarks'];
                    $option[$v['qtype']][$v['step_id']][$k] = $v;
                    $array_2d = array_values(array_values(current($option)));
                    $choice[$v['qtype']][$v['sequencenumer']]['option'] = $this->constitute_choice($array_2d);
                    $combination['question'] = $choice;
                    break;
                case 'shortanswer':
                    $short[$v['qtype']][$v['sequencenumer']]['id'] = $v['step_id'];
                    $short[$v['qtype']][$v['sequencenumer']]['sequencenumer'] = $v['sequencenumer'];
                    $short[$v['qtype']][$v['sequencenumer']]['score'] = $v['score'];
                    $short[$v['qtype']][$v['sequencenumer']]['grade'] = $v['grade'];
                    $short[$v['qtype']][$v['sequencenumer']]['qtype'] = $v['qtype'];
                    $short[$v['qtype']][$v['sequencenumer']]['questiontext'] = $v['questiontext'];
                    $short[$v['qtype']][$v['sequencenumer']]['img'] = $v['question_img'];
                    $short[$v['qtype']][$v['sequencenumer']]['generalfeedback'] = $v['generalfeedback'];
                    $short[$v['qtype']][$v['sequencenumer']]['remarks'] = $v['remarks'];
                    $answer[$v['qtype']]['answer'] = $v['answer'];
                    $short[$v['qtype']][$v['sequencenumer']]['answer'] = $answer;
                    //$short[$v['qtype']][$v['sequencenumer']]['answer']
                    break;
            }

        }
        pe($combination);
    }
    /**
     * 选择题的部分组合方法
     * @author 陈博文
     * @return array  $arr   组合后格式数组
     */
    public function constitute_choice($array_2D) {
        foreach ($array_2D as $val) {
            $i = 0;
            foreach ($val as $value) {
                $think[$i]['id'] = $value['data_id'];
                $think[$i]['answer'] = $value['answer'];
                $think[$i]['tab'] = $value['tab'];
                $think[$i]['img'] = $value['answer_img'];
                $think[$i]['fraction'] = $value['fraction'];
                $i++;
            }
        }
        return $think;
    }
    /**
     * 某试卷场次的答卷
     */
    public function sheetIndex() {
        $time = time();
        $data = $this->data; 
        $verify = ['id' => '参数错误！', 'examtype' => '参数错误！'];
        foreach ($verify as $key => $value) {
            if (!isset($data[$key])) {
                return getBack(1, $value);
            }
        }
        //获取用户角色
        $examClassroomQuiz = model('ExamClassroomQuiz');
        $where = ['cu.user_id' => $data['uid'], 'ecq.status' => 0, 'ecq.examtype' => $data['examtype']];
        if ($data['examtype'] == 1) {//考试
            $join = [
                ['__EXAM_CLASSROOM_PROJECT__ ecp', 'ecp.quiz = ecq.id'],
                ['__CLASSROOM_USER__ cu', 'cu.classroom_id = ecp.classroom']
            ];
            $field = 'cu.role, ecp.starttime, ecp.endtime, ecq.preferredbehaviour';
            $where['ecp.id'] = $data['id'];
        } else {//测试
            $join = [
                ['__CLASSROOM__ c', 'c.course_id = ecq.classroomid'],
                ['__CLASSROOM_USER__ cu', 'cu.classroom_id = c.id']
            ];
            $where['ecq.id'] = $data['id'];
            $where['c.status'] = 0;
            $field = 'cu.role';
        }
        $info = $examClassroomQuiz->alias('ecq')->join($join)->where($where)->field($field)->find();
        if (!$info) {
            return getBack(1, '查询错误！');
        }
        $info = $info->toArray();

        $configPage[config('paginate.var_page')] = isset($data['page']) ? ($data['page'] < 1 ? 1 : $data['page']) : 1;
        $configPage['list_rows'] = isset($data['rows']) ? ($data['rows'] < 1 ? config('paginate.list_rows') : $data['rows']) : config('paginate.list_rows');

        $examClassroomQuizAttempts = model('ExamClassroomQuizAttempts');
        $field = 'ecqa.id, user.number, user.realname as name, class.name as classname, ecqa.timestart as starttime, ecqa.usedtime as limittime, ecqa.finished as status, ecqa.sumgrades as grade';
        $join = [
            ['__USER__ user', 'user.id = ecqa.userid'],
            ['__CLASS__ class', 'class.id = user.class']
        ];
        $where = ['ecqa.status' => 0, 'user.status' => 0, 'class.status' => 0, 'ecqa.project' => $data['id']];
        switch ($data['examtype']) {
            case '1'://考试
                $join[] = [
                    ['__EXAM_CLASSROOM_PROJECT__ ecp', 'ecp.id = ecqa.project']
                ];
                if ($info['role'] == 1) {//学生
                    if ($info['preferredbehaviour'] == 1) {
                        return getBack(1, '不能查看该答卷！');
                    }
                    if ($time <= $info['endtime']) {
                        return getBack(1, '考试未结束！');
                    }
                    $where['ecp.status'] = 0;
                    $where['ecqa.userid'] = $data['uid'];
                } else {//老师
                    $where['ecp.status'] = 0;
                }
                break;
            case '0'://测试
                if ($info['role'] == 1) {//学生
                    $where['ecqa.userid'] = $data['uid'];
                }
                break;
            default:
                return getBack(1, '参数错误！');
                break;
        }
        $list = $examClassroomQuizAttempts->alias('ecqa')->join($join)->where($where)->field($field)->order('ecqa.timestart')->paginate($configPage, config('paginate.custom'))->toArray();
        $list = $this->dataPageDispose($list);
        foreach ($list['data'] as $k => $v) {
            $list['data'][$k]['starttime'] = date('Y-m-d H:i:s', $v['starttime']);
            $h = floor($v['limittime']/3600);
            $m = floor(($v['limittime'] - ($h * 3600)) / 60);
            $s = $v['limittime'] - ($h * 3600) - ($m * 60);
            if ($h > 0) {
                $list['data'][$k]['limittime'] = $h . '时' . $m . '分' . $s . '秒';
            } else if ($m > 0) {
                $list['data'][$k]['limittime'] = $m . '分' . $s . '秒';
            } else {
                $list['data'][$k]['limittime'] = $s . '秒';
            }
        }
        return getBack(0, $list);
    }
}