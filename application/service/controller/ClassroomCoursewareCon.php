<?php
namespace app\service\controller;
use app\service\model\classroomCourseware;

class ClassroomCoursewareCon extends Common
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
    public function addClassCourseware() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $course = new classroomCourseware();

    }
    /*
     * 课程编辑实现
     * @param 类型 参数名 参数说明
     */
    public function upClassCourseware() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $course = new classroomCourseware();
    }
    /*
     * 课程查询实现
     * @param 类型 参数名 参数说明
     */
    public function queClassCourseware() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //通过$data['que_type']的值判断查询方式  直接传数据表字段
        $course = new classroomCourseware();

    }
    /*
     * 课程删除实现
     * @param 类型 参数名 参数说明
     */
    public function delClassCourseware() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //通过$data['del_type']的值和用户角色判断删除方式  一般的修改status值，最高权限直接从数据表删除
        $course = new classroomCourseware();

    }

}