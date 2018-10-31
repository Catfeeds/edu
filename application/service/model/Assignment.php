<?php
namespace app\service\model;
use think\Model;

class Assignment extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }
    protected $pk = 'id';
    //课程新增id自动完成
    protected $auto = ['id'];
    protected $insert = ['status', 'timecreated'];  
    protected function setStatusAttr() {
        return 0;
    }
    protected function setTimecreatedAttr() {
        return time();
    }
    protected function setIdAttr($val) {
        return $val ? $val : createGuid();
    }
    /**
     * @author 陈博文
     * 存作业公共部分
     */
    public function addAssignmentCommon($data, $pid = null) {
        $idnumber = $this->conbineIdnumber($data['idnumber'], $data['courseid']);
        foreach ($data['upload_files'] as $k => $v) {
            if ($v['flag'] == 'homework') {
                $homework_file[] = $v;
            }
            if ($v['flag'] == 'answer') {
                $all_hashs_answer[] = $v['hash'];
                $all_files_answer[] = $v['file'];
            }
            if ($v['hash'] == $data['msglist'][$k]['hash']) {
                $homework_file[$k]['newname'] = $data['msglist'][$k]['newname'];
            }
        }
        $sectionid = join(',', $data['sectionid']);
        $info['auth'] = $data['uid'];
        $info['idnumber'] = $idnumber;
        $info['sectionid'] = $sectionid;
        $info['courseid'] = $data['courseid'];
        $info['type'] = $data['type'];
        $info['name'] = $data['name'];
        $info['explain'] = $data['explain'];
        $info['parentstr'] = '0,';
        if (!empty($pid)) {
            for ($i = 0; $i < count($data['answer']); $i++) {
                if ($data['answer'][$i]['question'] != null) {
                    $insert[$i] = $info;
                    $insert[$i]['name'] = $data['answer'][$i]['question'];
                    $insert[$i]['parentstr'] = '0,' . $pid;
                    $insert[$i]['pid'] = $pid;
                    unset($insert[$i]['idnumber']);
                }
            }
            $object_data = collection($result = $this->saveAll($insert))->toArray();
            foreach ($object_data as $v) {
                $children_ids[] = $v['id'];
            }
            if ($result) {
                return ['all_hashs_answer' => $all_hashs_answer, 'all_files_answer' => $all_files_answer, 'children_ids' => $children_ids];
            }
            
        }
        //存作业描述，获取新增作业id；
        $this->data($info)->allowField(true)->save();
        if (empty($pid)) {
            $new_assId = $this->id;
            return $new_assId;
        }
    }
    /**
     * @author  陈博文
     * 存作业和答案以及调用存作业文件和答案文件的方法
     */
    public function addAssignment($data){
        $attachments = new Attachments();
        $assignment_answer = new AssignmentAnswer();
        $assignment_file = new AssignmentFiles();
        //调用存作业公共部分
        $new_assId = $this->addAssignmentCommon($data, null);
        //存作业子题
        $return_data = $this->addAssignmentCommon($data, $new_assId);
        //pe($return_data);
        //存作业文件
        $assignment_file->insertAssignFiles($data['upload_files'], $data['msglist'], $new_assId, $data['courseid'], $data['uid']);
        //存作业答案
        $assignment_answer->insertAnswer($return_data['all_hashs_answer'], $return_data['all_files_answer'], $data, $new_assId, $return_data['children_ids']);
    }
    
    /**
     * 组合作业编号
     * @author 陈博文 
     */
    public function conbineIdnumber($idnumber, $courseid) {
        $course = new Course();
        $info = $this->where(['status' => 0, 'courseid' => $courseid])->find();
        if ($info) {
            $timecreated = $this->where('status', 0)->max('timecreated');
            $idnumberOld = $this->where(['status' => 0, 'pid' => 0, 'timecreated' => $timecreated])->value('idnumber');
            if ($idnumberOld) {
                preg_match_all('/[a-zA-Z]+/', $idnumberOld, $matches);
                preg_match_all('/\d+/', $idnumberOld, $new_idnumberOld);
                $new_idNum = $matches[0][0] . ($new_idnumberOld[0][0] + 1);
                return $new_idNum;
            } else {
                echo getBack(1, "未查询到相关信息");
            }
        } else {
            if ($idnumber == null) {
                $idnumberOld = $course->where('status', 0)->value('idnumber');
                if ($idnumberOld) {
                    return $idnumberOld . "001";
                } else{
                    echo getBack(1, "未查询到相关信息");
                }
            }
        }
    }

    /*@author  陈博文
     *组合作业序号
     *@param $id      课程id
     */
    public function checkIt($id)
    {
        /*$this->save($data);
        $course = new Course();
        $course_idnumber = $course->where('status',0)->where('id',$id)->value('idnumber');
        $homeWork = $this->where('courseid',$id)->where('status',0)->select();
        if ($homeWork == null) {
            $num1 = $course_idnumber . "001";
            return $num1;
        } else {
            $num2 = $this->max('number') + 1;
            $max_num = $course_idnumber . $num2;
            return $max_num;
        }*/
    }
    /**根据课程id获取作业列表
     * @author 叶文
     * @param $date id 课程id row每页显示条数 page显示第几页
     * @return string
     */
    public function homeworkList($data) {
        $assignment = new Assignment;
        $course = new Course();
        $cou_info = $course->where(['status' => 1, 'id' => $data['id']])->field('id, fullname')->find();
        $configPage['list_rows'] = config('paginate.list_rows');
        isset($data['row']) && $data['row'] > 0 && $configPage['list_rows'] = $data['row'];
        $configPage[config('paginate.var_page')] = 1;
        isset($data['page']) && $data['page'] > 0 && $configPage[config('paginate.var_page')] = $data['page'];
        $join = [
            ['__COURSE__ cou', 'cou.id = ass.courseid'],
            ['__COURSE_SECTIONS__ sec', 'sec.id = ass.sectionid'],
            ['__USER__ user', 'user.id = ass.auth']
        ];
        $where = [
            'ass.status' => 0,
            'ass.courseid' => $data['id'],
            'cou.status' => 1,
            'sec.status' => 0
        ];
        $field = 'ass.id, ass.sectionid, ass.name, ass.explain, ass.type, ass.idnumber, sec.name as sectionname, user.realname as author, ass.timecreated as createtimestamp';
        if(empty($data['section'])){
            $res = $assignment->alias('ass')->where($where)->order('number')->join($join)->field($field)->paginate($configPage, config('paginate.custom'))->toArray();
        }else {
            $where['sec.status'] = $data['section'];
            $res = $assignment->alias('ass')->where($where)->order('number')->join($join)->field($field)->paginate($configPage, config('paginate.custom'))->toArray();
        }
        $res['courseid'] = $cou_info['id'];
        $res['coursename'] = $cou_info['fullname'];
        array_walk($res['data'], function(&$val, $key){
            $val['createtime'] = date('Y-m-d', $val['createtimestamp']); 
        });
        return getBack(0, $res);
    }
    /**获取作业详细信息以及答案信息
     * @author 叶文
     * @param $date id作业id courseid 班级id section 章节id
     * @return string
     */
    public function anserList($date)
    {
        $assignment = new Assignment;
        $assignmentfile = new AssignmentFiles;
        $course_sections = new CourseSections;
        $id = $date['id'];
        $courseid = $date['courseid'];
        $section = $date['sectionid'];
        $ret = $course_sections->where(['status' => 0, 'courseid' => $courseid, 'id' => $section])->find();
        if ($ret) {
            $homework = $assignment->where('status', 0)->find($id);
            if ($homework['file_number'] > 0) {
                $files = $assignmentfile->field('id,name,summary,extendname,attachments')->where(['status' => 0, 'courseid' => $id])->select();
                $homework['files'] = $files;
            }
            $answer = new AssignmentAnswer;
            $res = $answer->field('id,answer,attention')->where(['assignmentid' => $id, 'status' => 0])->select();
            if ($res) {
                foreach ($res as $v) {
                    $arr[] = $v['id'];
                }
                $file = $assignmentfile->field('id,name,summary,extendname,attachments')->where('status', 0)->where('courseid', 'in', $arr)->select();
            } else {
                $file = array();
            }
            $result['homework'] = $homework;
            $result['answer']['answer'] = $res;
            $result['answer']['files'] = $file;
            return getBack(0,$result);
        }else {
            return getBack(1,"数据库内部错误，请于管理员联系");
        }
    }

    /**删除课程作业
     * @param $idt 课程作业数组 id:作业id courseid=课程id sectionid=章节id
     * @return string
     */
    public function delHomework($idt) {
        $assignment = new Assignment;
        $ids = array();
        foreach ($idt as $k=>$v){
            $ids[$k] = $v['id'];
        }
        $tt = $assignment->where('id','in', $ids)->select();
        foreach ($tt as $k=>$v) {
            $err[$k]['courseid'] = $v['courseid'];
            $err[$k]['sectionid'] = $v['sectionid'];
        }
        foreach ($idt as $k=>$v) {
            if ($v['courseid']!= $err[$k]['courseid'] || $v['sectionid']!=$err[$k]['sectionid']){
                unset($idt[$k]);
            }
        }
        $idt = array_merge_recursive($idt);
        foreach ($idt as $k=>$v) {
            $sids[$k] = $v['id'];
        }
        foreach ($sids as $k=>$v) {
            $arr[$k]['id'] = $v;
            $arr[$k]['status'] = 1;
            $arr[$k]['timemodified'] = time();
        }
        $re = $assignment->saveAll($arr);
        if($re!==false) {
            $assignmentfile = new AssignmentFiles;
            $res = $assignmentfile->field('id,attachments')->where('status',0)->where('assignmentid','in', $sids)->select();
            if($res) {
                foreach ($res as $k=>$v) {
                    $trr[$k]['id'] = $v['id'];
                    $trr[$k]['status'] = 1;
                    $trr[$k]['timemodified'] = time();
                    $crr[$k] = $v['attachments'];
                }
                $assignmentfile->saveAll($trr);
                $prr = array();
                foreach ($crr as $k=>$v) {
                    if(!in_array($v, $prr)){
                        $prr[] = $v;
                    }
                }
                $attachments = new Attachments;
                $ret = $attachments->field('id,use_number')->where('id','in', $prr)->select();
                if ($ret) {
                    foreach ($ret as $k=>$v) {
                        $i = 0;
                        foreach ($crr as $k2=>$v2){
                            if($v['id'] == $v2) {
                                $i = $i+1;
                            }
                        }
                        $lrr[$k]['id'] = $v['id'];
                        $lrr[$k]['use_number'] = $v['use_number']-$i;
                        $lrr[$k]['timemodified'] = time();
                    }
                    $attachments->saveAll($lrr);
                }
            }
        }
        return getBack(0,"删除成功");
    }
    /**@author  李桐
     *更新作业
     */
    public function uphomework($data){
        if (!$validates->check($data)) {
            return getBack(1, $validates->getError());
        }
        $Attachments = new Attachments();
        $assignment_file = new AssignmentFiles();
        if(isset($data['homework']['files'][0])){
            $assignment_file->upsummary($data['homework']['files'][0]['id'], $data['homework']['files'][0]['summary']);
        }
        if(isset($data['homework']['delfile'][0])){
            $assignment_file->delCourseware($data['homework']['delfile'][0]['id']);
        }
        if(isset($data['upload_files'])){
            for($i = 0; $i < count($data['upload_files']); $i++){
                if($data['upload_files'][$i]['flag'] == "homework"){
                    $fileId = $Attachments->saveFile($data['upload_files'][$i]['hash'], $data['upload_files'][$i]['file']);
                    $list['attachments'] = $fileId;
                    $list['assignmentid'] = $data['homework']['id'];
                    $list['courseid'] = $data['courseid'];
                    $list['name'] = $data['upload_files'][$i]['name'];
                    $list['summary'] = $data['upload_files'][$i]['summary'];
                    $list['status'] = "0";
                    $list['timecreated'] = time();
                    $list['auth'] = $data['uid'];
                    $assignment_file->data($list)->isUpdate(false)->allowField(true)->save();
                    $file_nun = $this->where('id',$data['homework']['id'])->where('status', '0')->value('file_number');
                    $file_num = $file_num+1;
                    $mes['file_number'] = $file_num;
                }
            }
        }
        $mes['name'] = $data['homework']['name'];
        $mes['explain'] = $data['homework']['explain'];
        $mes['timemodified'] = time();
        $res = $this->allowField(true)->save($mes, ['id' => $data['homework']['id']]);
        if($res){
            return getBack(0,1);
        }else{
            return getBack(1,"更新失败");
        }
    }

}