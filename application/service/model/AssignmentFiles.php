<?php
namespace app\service\model;
use think\Model;

class AssignmentFiles extends Model
{
    protected function initialize() {
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
    /**@author  李桐
     *更新作业文件(课件)描述
     *@param char id            作业文件id，定位需要修改作业文件描述的位置
     *@param text summary       修改作业文件描述
     *
     */
    public function upSummary($id, $summary){
        $time = time();
        $res = $this->save(['summary' => $summary, 'timemodified' => $time],['id' => $id]);
        if ($res) {
            return getBack(0, 1);
        } else {
            return getBack(1, '修改描述失败');
        }
    }
    /**@author  李桐
     *删除作业文件(课件)信息
     *@param char id            作业文件id，定位需要删除作业文件的位置
     *删除课件信息并到表Attachments中将引用次数减一
     */
    public function delCourseware($id){
        $time = time();
        $fileId = $this->where('id', $id)->where('status', '0')->value('attachments');
        $this->save(['status' => '1', 'timemodified' => $time],['id' => $id]);
        $atta = new Attachments();
        $res = $atta->updateFile($fileId);
        if ($res) {
            return getBack(0, 1);
        } else {
            return getBack(1, '删除失败');
        }
    }
    /*@author  陈博文
     *存作业文件
     *@param $homework_file     作业文件数组
     *@param $new_assId         新增的作业id
     */
    public function insertAssignFiles($upload_files, $msglist, $new_assId, $courseid, $auth) {
        $assignment = new Assignment();
        $attachments = new Attachments();
        if (!empty($upload_files[0]['file'])) {
            for($i = 0; $i < count($upload_files); $i++) {
                $idm[] = $attachments->saveFile($upload_files[$i]['hash'], $upload_files[$i]['file']);//获取的作业文件数组
                $info['attachments'] = $idm[$i];
                $info['assignmentid'] = $new_assId;
                $info['courseid'] = $courseid;
                $info['name'] = $upload_files[$i]['name'];
                $info['auth'] = $auth;
                $info['summary'] = $upload_files[$i]['summary'];
                $info['newname'] = $msglist[$i]['newname'];
                $this->data($info)->isUpdate(false)->allowField(true)->save();//存作业文件
            }
            $assignment->save(['file_number' => '1'],['id' => $new_assId]);//更新file_number字段的值
        }
    }
    /*@author  陈博文
     *存作业答案文件
     *@param $all_hashs         答案文件hash
     *@param $all_files         答案文件对象
     *@param $all_answerID      新增的所有答案id
     */
    public function insertAssignAnswerFiles($all_files, $data, $all_hashs, $all_answerID) {
        //pe($all_answerID);
        $assignment_answer = new AssignmentAnswer();
        $courseWare = new Courseware();
        $attachments = new Attachments();
        if ($all_files) {
            $list['author'] = $data['uid'];
            $list['type'] = '这里是课件类型';
            for ($i = 0; $i < count($all_hashs); $i++) {
                $list['attachments'] = $attachments->saveFile($all_hashs[$i], $all_files[$i]);
                $list['timecreated'] = time();
                $courseWare->data($list)->isUpdate(false)->save();
                //对file_number字段的更新
                $assignment_answer->where('id', 'in', $all_answerID)->update(['file_number' => '1']);
                $this->
            }
        }
    }
    /*@author  陈博文
     *作业文件表中存入答案id
     *@param $answerIDS      作业答案id数组
     */
    public function inAssignInsertAnswerId($answerIDS) {
        for($i = 0; $i < count($answerIDS); $i++) {
            $data['assignmentanswer'] = $answerIDS[$i];
            $this->data($data)->isUpdate(false)->allowField(true)->save();
            }
        }
    /**
     * 根据作业id得到作业文件（作业和作业答案）或者只要作业答案文件或者只要作业文件
     * @param  string $course 课程id
     * @param  string $assignment 作业id
     * @param  string $str 返回的字段
     * @param  string $type   默认为所有，homework：只要作业文件；answer：只要答案文件
     */
    public function getfiels($course, $assignment, $str = '*', $type = null) {
        $where = ['courseid' => $course, 'assignmentid' => $assignment, 'status' => 0];
        switch ($type) {
            case 'homework':
                $where['assignmentanswer'] = 1;
                break;
            case 'answer':
                $where['assignmentanswer'] = 2;
                break;
        }
        $str .= ',assignmentanswer';
        $homework_file_info = $assignmentfile->where($where)->field($str)->select();
        if (!$homework_file_info) {
            return false;
        }
        //将作业文件和作业答案文件分类
        if ($type === null) {
            $data = [];
            foreach($homework_file_info as $val) {
                switch ($val['assignmentanswer']) {
                    case 1:
                        $data['homweork'][] = $val;
                        break;
                    case 1:
                        $data['answer'][] = $val;
                        break;
                }
            }
            return $data;
        }
        return $homework_file_info;
    }
    


}