<?php
namespace app\service\controller;
use app\service\model\Classroom;

class ClassroomCon extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /*
     * 课程添加实现
     * @param 类型 参数名 参数说明
     * pid课程父id ,department系id,category分类id,fullname全名,shortname短名,idnumber课程编号,summary概要,coursepic课程图片,startdate开始时间,type属性
     * status状态,numsections周期
     */
    public function addClassroom() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $classroom = new Classroom();
        $add = $classroom->TakeForCourse($data);
        return $add;

    }
    /*
     * 课程编辑实现
     * @param 类型 参数名 参数说明
     */
    public function upClassroom() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $classroom = new Classroom();
        $update = $classroom->updateClassroomMsg($data);
        return $update;
    }
    /*
     * classroom删除实现
     * @param 类型 参数名 参数说明
     */
    public function delClassroom() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //通过$data['del_type']的值和用户角色判断删除方式  一般的修改status值，最高权限直接从数据表删除
        $classroom = new Classroom();
        $result = $classroom->delClassroom($data);
        return $result;

    }
    /**@author  叶文
     * 查询用户所有的课堂
     */
    public function userClassroom() {
        $data = $this->data;
        $classroom = new Classroom();
        if(@!empty($data['page'])){
            $page = $data['page'] < 1 ? 1 : $data['page'];
        }else {
            $page = 1;
        }
        if(@!empty($data['list_rows'])){
            $list_rows = $data['list_rows'] < 1 ? 1 : $data['list_rows'];
        }else {
            $list_rows = config('paginate.list_rows');
        }
        if (@!empty($data['id'])) {
            $id = $data['id'];
        } else {
            $id = $data['uid'];
        }
        $res = $classroom->userAllClassroom($id, $page, $list_rows);
        return $res;
    }
    /**@author  叶文
     * 获取课堂以及章节信息
     */
    public function classroomInfo() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //通过$data['que_type']的值判断查询方式  直接传数据表字段
        $classroom = new Classroom();
        $id = $data['id'];
        $res = $classroom ->CourseMsg($id);
        if($res) {
            return getBack(1, $res);
        }else {
            return getBack(0, '获取信息失败');
        }
    }
    /*
     * classroom_sections删除实现
     * @param 类型 参数名 参数说明
     */
    public function delClassroomSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $classroom = new Classroom();
        $result = $classroom->delClassroomSections($data);
        return $result;
    }
    /*
     * 表Attachment附件引用次数减一
     * @param 类型 参数名 参数说明
     */
    public function delAttachments() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $classroom = new Classroom();
        $result = $classroom->delAttachments($data);
        return $result;
    }
    /*
     * 表classroom_user用户删除
     * @param 类型 参数名 参数说明
     */
    public function delClassroomUser() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $classroom = new Classroom();
        $result = $classroom->delClassroomUser($data);
        return $result;
    }
    /**
     * 课堂信息
     * @author 陈博文 
     */
    public function getInformation() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        if (@empty($data['id'])) {
            return getBack(1, '参数错误！');
        }
        $classroom = new Classroom();
        $classroomUser = model('ClassroomUser');
        $name = $classroom->where(['id' => $id, 'status' => 0])->value('fullname');
        if (!$name) {
            return getBack(1, '课堂不存在！');
        }
        $info = $classroomUser->where(['classroom_id' => $id, 'role' => 1])->select();
        $teacher = $classroomUser->alias('classroomUser')
        ->join('__USER__ user', 'classroomUser.user_id = user.id')
        ->where(['classroomUser.classroom_id' => $id, 'classroomUser.role' => 0])
        ->field('user.realname')->select();
        $teacherName = '';
        foreach ($teacher as $value) {
            $teacherName .=  ',' . $value->realname;
        }
        $teacherName = mb_substr($teacherName, 1);
        return getBack(0 ,['name' => $name, 'number' => count($info), 'teacher' => $teacherName]);
    }

}