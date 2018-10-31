<?php
namespace app\service\controller;
use app\service\model\ClassroomSections;

class ClassroomSectionsCon extends Common
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
    public function addClassSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $course = new ClassroomSections();

    }
    /*
     * 课程编辑实现
     * @param 类型 参数名 参数说明
     */
    public function upClassSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $course = new ClassroomSections();
    }
    /*
     * 课程查询实现
     * @param 类型 参数名 参数说明
     */
    public function queClassSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //通过$data['que_type']的值判断查询方式  直接传数据表字段
        $course = new ClassroomSections();

    }
    /*
     * 课程删除实现
     * @param 类型 参数名 参数说明
     */
    public function delClassSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //通过$data['del_type']的值和用户角色判断删除方式  一般的修改status值，最高权限直接从数据表删除
        $course = new ClassroomSections();

    }
    /*
     * 章节列表
    */
    public function getSections() {
        $data = $this->data;
        if (!isset($data['id'])) {
            return getBack(1, '参数错误！');
        }
        $classroomSections = model('ClassroomSections');
        $info = collection($classroomSections->where(['classroomid' => $data['id'], 'status' => 0])->field('id, name, summary, pid, parentstr, type, section, sort')->order('sort')->select())->toArray();
        if (!$info) {
            return getBack(1, '暂无数据！');
        }
        $info = getClassify_index($info, 'id', 'pid', config('pid_default'));
        if ($info) {
            return getBack(0, $info);
        } else {
            return getBack(1, '暂无数据！');
        }
    }

}