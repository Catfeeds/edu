<?php
namespace app\service\controller;
use app\service\model\CourseSections;

class CourseSectionsCon extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /*
     * 章节添加实现
     * @param 类型 参数名 参数说明
     */
    public function addSections() {
       $data = $this->data;//前端传递过来的所有数据，以数组的形式存
       $sections = new CourseSections();
       $result = $sections->add($data);
       return $result;
    }
    /*
     * 章节修改实现
     * @param 类型 参数名 参数说明
     */
    public function upSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $sections = new CourseSections();
        $result = $sections->up($data);
        return $result;
    }
    /*
     * 章节查询实现
     * @param 类型 参数名 参数说明
     */
    public function queSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //两级结构，一级为章，二级为节，和无限级分类相似，章与章，节与节按sort顺序排序；可通过课程id来查询
        $sections = new CourseSections();
        $result = $sections->que($data);
        return $result;
    }
    /*
     * 章节删除实现
     * @param 类型 参数名 参数说明
     */
    public function delSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $sections = new CourseSections();
        //通过修改状态达到删除
        $result = $sections->del($data);
        return $result;
    }
    /*
     * 章节排序实现
     * @param 类型 参数名 参数说明
     */
    public function rankSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $sections = new CourseSections();
        $result = $sections->rank($data);
        return $result;
    }
}