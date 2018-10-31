<?php
namespace app\service\model;
use think\Model;

class AssignmentAnswer extends Model
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
    /**@author  李桐
     *更新答案
     */
    public function upanswer($data){
        /*测试数据
        $a = array('answer'=>array('answer'=>array('0'=>array('id' => '123', 'name' => 'ceshi1', 'attention' => 'ceshi1')), 'files'=>array('0'=>array('id' => '456', 'summary' => 'ceshi')), 'delanswer'=>array('0'=>array('id'=>'456'))));
        $data = $a;*/

        if (!$validates->check($data)) {
            return getBack(1, $validates->getError());
        }
        $Attachments = new Attachments();
        $assignment_file = new AssignmentFiles();
        if(isset($data['answer']['files'][0])){
            $assignment_file->upsummary($data['answer']['files'][0]['id'], $data['answer']['files'][0]['summary']);
        }
        if(isset($data['answer']['delanswer'][0])){
            $assignment_file->delCourseware($data['answer']['delanswer'][0]['id']);
        }
        if(isset($data['upload_files'])){
            for($i = 0; $i < count($data['upload_files']); $i++){
                if($data['upload_files'][$i]['flag'] == "answer"){
                    $fileId = $Attachments->saveFile($data['upload_files'][$i]['hash'], $data['upload_files'][$i]['file']);
                    $list['attachments'] = $fileId;
                    $list['assignmentid'] = $data['answer']['answer'][0]['id'];
                    $list['courseid'] = $data['courseid'];
                    $list['name'] = $data['upload_files'][$i]['name'];
                    $list['summary'] = $data['upload_files'][$i]['summary'];
                    $list['status'] = "0";
                    $list['timecreated'] = time();
                    $list['auth'] = $data['uid'];
                    $assignment_file->data($list)->isUpdate(false)->allowField(true)->save();
                    $file_nun = $this->where('id',$data['answer']['answer'][0]['id'])->where('status', '0')->value('file_number');
                    $file_num = $file_num+1;
                    $mes['file_number'] = $file_num;
                }
            }
        }
        $mes['attention'] = $data['answer']['answer'][0]['attention'];
        $mes['timemoddified'] = time();
        $res = $this->allowField(true)->save($mes, ['id' => $data['answer']['answer'][0]['id']]);
        if($res){
            return getBack(0,1);
        }else{
            return getBack(1,"更新失败");
        }
    }
    /*@author  陈博文
     *存作业答案描述以及调用存答案文件的方法
     *@param $all_hashs         答案文件hash
     *@param $all_files         答案文件对象
     *@param $new_assId         新增的作业id
     */
    public function insertAnswer($all_hashs, $all_files, $data, $new_assId, $children_ids) {
        $info['auth'] = $data['uid'];
        $assignment_file = new AssignmentFiles();
        //存多个答案描述
        $num = 0;
        foreach ($data['answer'] as $v) {
            if ($v['question'] == null) {
                $attention[] = $v;
            } else {
                $num++;
            }
        }
        $info['assignmentid'] = $new_assId;
        $info['courseid'] = $data['courseid'];
        foreach ($data['answer'] as $v) {
            if ($v['question'] == null) {
                $marg['answer'] = $v['answer'];
                $marg['attention'] = $v['attention'];
            }
        }
        $new_arr = array_merge($info, $marg);
        //存大题答案
        $this->data($new_arr)->allowField(true)->save();
        $main_answer_id = $this->id;
        //存子题答案
        for ($i = 0; $i < $num; $i++) {
            if ($data['answer'][$i]['question'] != null) {
                $mess['courseid'] = $data['courseid'];
                $mess['attention'] = $data['answer'][$i]['attention'];
                $mess['answer'] = $data['answer'][$i]['answer'];
                $mess['assignmentid'] = $children_ids[$i];
            }
            $this->data($mess)->isUpdate(false)->allowField(true)->save();//作业答案文件和描述存进课件表
            $childrens_answerID[$i] = $this->id;
        }
        //pe($new_assId);
        $all_answerID = array_merge([$main_answer_id], $childrens_answerID);
        //调用存作业答案文件方法
        $assignment_file->insertAssignAnswerFiles($all_files, $data, $all_hashs, $all_answerID);
        die;
        //在作业文件表中,存入作业答案id
        $assignment_file->inAssignInsertAnswerId($answerIDS);
    }

}