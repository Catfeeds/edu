<?php
namespace app\exam\controller;

use app\service\controller\Common;
use think\console\command\make\Model;
use app\service\model\ClassroomUser;
use app\service\model\ExamClassroomQuiz;
use app\service\model\User;
use app\service\model\ClassroomSections;
class Project extends Common
{
    public function __construct(){
        parent::__construct();
    }
    /**
     * 场次审核
     * @author 莫博航
     */
    public function audit(){
        $data = $this->data;
        $project = model('ExamClassroomProject');
        if (@empty($data['status']) || @empty($data['id'])) {
            return getBack(1, '参数错误，缺少必要参数！');
        }
        switch ($data['status']) {
            case '1':
                $res = $project->save(['check' => 1], ['id' => $data['id']]);
                return getBack($res ? 0 : 1, $res ? "操作成功！" : "信息保存失败！");
                break;
            case '2':
                $msg = @empty($data['message']) ? '' : $msg = $data['message'];
                $res = $project->save(['check' => 5, 'msg' => $msg], ['id' => $data['id']]);
                return getBack($res ? 0 : 1, $res ? "操作成功！" : "信息保存失败！");
                break;
            default:
                return getBack(1, '参数值错误！');
                break;
        }
    }
    /**
     * 考试场次列表
     * @author 宋玉琪
     */
    public function index()
    {
        $data = $this->data;
        $project = model('ExamClassroomProject');
        $quiz = model('ExamClassroomQuiz');
        $where = array();
        $page = 1;
        $rows = config('paginate')['list_rows'];
        $bool = false;
        if (isset($data['name']) && (! is_null($data['name'])))
            $where['name'] = ['like', '%' . $data['name'] . '%'];
        if (isset($data['starttime'])&& (! is_null($data['starttime'])))
            $where['starttime'] = ['>=', strtotime($data['starttime'])];
        if (isset($data['endtime'])&& (! is_null($data['endtime'])))
            $where['endtime'] = ['<=', strtotime($data['endtime'])];
        if (isset($data['limittime'])&& ($data['limittime'] != ''))
            $where['limittime'] = $data['limittime'] * 60;
        if (isset($data['page'])&& (! is_null($data['page'])))
            $page = $data['page'];
        if (isset($data['rows'])&& (! is_null($data['rows'])))
            $rows = $data['rows'];
        if (isset($data['classroom'])&& (! $data['classroom'] == ''))
        {
            $where['classroom'] = $data['classroom'];
        } else {
            $classroomuser = new ClassroomUser();
            $classroomid = $classroomuser->where('user_id', $data['uid'])->value('classroom_id');
            if (is_null($classroomid))
                return getBack(1, '未能查询到你的课堂号');
            $where['classroom'] = $classroomid;
        }
        $where['status'] = 0;
        $sections = array();
        $base = $project->searchByWhere($where, $page, $rows)->toArray();
        $quizArray = [];
        foreach ($base['data'] as $item)
        {
            $quizArray[] = $item['quiz'];
        }
        $where = [
            'id' => ['in', $quizArray],
            'status' =>'0'
        ];
        $quizlist = collection($quiz->where($where)->select())->toArray();
        if (empty($base['data']))
            return getBack(1, '没有查询到考试场次');
        $list = $base['data'];
        $info = [
            'page' => [
                'total' => $base['total'],
                'per_page' => $base['per_page'],
                'current_page' => $base['current_page'],
                'last_page' => $base['last_page'],
            ],
            'data' => []
        ];
        if (isset($data['sections']) && !empty($data['sections']))
        {
            $sections = $data['sections'];
            $newdata = array();
            foreach ($list as $keys => $vals)
            {
                $list[$keys]['sections'] = explode(',', $list[$keys]['sections']);
                foreach($sections as $item)
                {
                    if(in_array($item,$list[$keys]['sections']))
                    {
                        $newdata[] = $list[$keys];
                        break;
                    }
                }
            }
            $list = $newdata;
        }
        foreach ($list as $item)
        {
            foreach($quizlist as $val)
            {
                if($val['id'] == $item['quiz'])
                {
                    $arr = array();
                    $arr['id'] = $item['id'];
                    $arr['name'] = $item['name'];
                    $arr['starttime'] = $item['starttime'];
                    $arr['endtime'] = $item['endtime'];
                    $arr['check'] = $item['check'];
                    $arr['classroomid'] = $item['classroom'];
                    $arr['quizname'] = $val['name'];
                    $arr['quiz'] = $val['id'];
                    $info['data'][] = $arr;
                }
            }
        }
        if (empty($info['data']))
        return getBack(0, '没有查询到考试场次');
        return getBack(0, $info);
    }
    /**
     * @User:贾芸吉
     * @Date:18/7/18 16:42
     * @type:创建场次
     */
	public function createProject(){
        $data = $this->data;
        $ecp = model('ExamClassroomProject');
        $ecqq= Model('ExamClassroomQuizQuestions');
        $ecpq = Model('ExamClassroomProjectQuestions');
        $where =[
            'id'       => $data['quiz'],
            'status'   => 0,
            'check'    => 6,
            'examtype' => 1
        ];
        $quiz= Model('ExamClassroomQuiz')
            ->where($where)
            ->field(['id','classroomid','sections', 'timelimit'])
            ->find();
        if (!$quiz) {
            return getBack(1, '选择创建试卷考试场次的试卷不符合创建条件！');
        }
        $info = [
                'quiz'        => $data['quiz'],
                'classroom'   => $quiz->classroomid,
                'sections'    => $quiz->sections,
                'name'        => $data['name'],//=> 场次名称
                'message'     => $data['message'], //=> 场次描述
                'starttime'   => strtotime($data['starttime']),//=> 开始时间 （Y-m-d H:i）
                'endtime'     => strtotime($data['endtime']), //=> 结束时间 （Y-m-d H:i）
                'limittime'   => $quiz->getData('timelimit'),
                'attempts'    => 1, //允许答题次数,此为创建考试场次，默认只有一次答题机会
                'check'       => 4,//创建考试成功，直接为待审核状态
                'auth'        =>$data['uid'],
                'timecreated' =>time()
        ];
        $res2 = $ecp->save($info);
        if (!$res2){
            return getBack(1,'场次创建失败');
        }
        $message = '场次创建成功';
        $sjdata = collection($ecqq->where(['quiz' => $data['quiz']])->select())->toArray();
        for ($i= 0;$i < count($sjdata);$i ++){  //试题添加
            $question[]=
                [
                    'projectid'  => $ecp->id,
                    'questionid' => $sjdata[$i]['questionid'],
                    'qtype'      => $sjdata[$i]['qtype'],
                    'seq'        => $sjdata[$i]['seq'],
                    'score'      => $sjdata[$i]['score']
                ];
        }
        $result = $ecpq->saveAll($question);
        if (!$result){
            return getBack(1, '试题插入失败！');
        }
        $message .= '试题插入成功';
        return getBack(0,$message);
    }
	/**
	 * 删除试卷考试场次
	 * @author 宋玉琪
	 */
	public function delProject()
	{
		$data = $this->data;
		$project = model('ExamClassroomProject');
		$res = $project->save(['status' => 1], ['id' =>$data['id']]);
		if($res)
		return getBack(0,'删除成功');
		return getBack(1,'删除失败');
	}
   /**
 * 查看试卷考试场次详情
 * @author 李存亮
 */
public function details() {
        $data = $this->data;
        $examClassroomProject = model('ExamClassroomProject');
        $info = $examClassroomProject
                ->where('id',$data['id'])
                ->where('status',0)
                ->field('timemodified, status, timecreated',true)
                ->find();
        if($info){
            $info = $info->toArray();
        } else{
            return getback(1,"该考试场次不存在！");
        }
        $examClassroomQuiz = model('ExamClassroomQuiz');
        $info['quiz_name'] = $examClassroomQuiz->where(['id' => $info['quiz'], 'status' => 0])->value('name');
        if( $info['quiz_name'] ){
            $user = new User();
            $info['auth'] = $user->where('id',$info['auth'])->value('realname');
            $res = explode(',', $info['sections']);
            $classroomSections = new ClassroomSections();
            $sec = collection($classroomSections
                  ->where('id','in',$res)
                  ->where('status',0)
                  ->where('classroomid',$info['classroom'])
                  ->field('name,type')
                  ->select())->toArray();
            if($sec){
                for ($i=0; $i <count($sec) ; $i++) { 
                    if($sec[$i]['type'] == 0){
                        $section[$i] = $sec[$i]['name'];
                    } else{
                        $sections2[$i] = $sec[$i]['name'];
                    }
                } 
                if(! isset($sections2))
                {
                    $sections2 = null;
                }
                $info['sections'] = array("section" => $section, "sections" => $sections2);
            } else{
                return getback(1, "该场次有关的章节都不存在！");
            }
            return getback(0,$info);
        } else{
            return getback(1,"创建该试卷场次的试卷不存在！");
        }
    }
    /**
     * 编辑试卷考试场次
     * @author 陈博文
     */
    public function editProject() {
        $data = $this->data;
        $project = model('ExamClassroomProject');
        $total_time = $project->where('id', $data['id'])->field('starttime, endtime')->find()->toArray();
        $starttime = strtotime($total_time['starttime']);
        $endtime = strtotime($total_time['endtime']);
        if ($starttime < time() || time() > $endtime) {
            return getBack(1, '超出考试时间编辑范围');
        }
        $info = $project->allowField(true)->save($data,['id' => $data['id']]);
        if ($info) {
            return getBack(0, '编辑成功');
        } else {
            return getBack(1, '编辑失败');
        }
    }
}