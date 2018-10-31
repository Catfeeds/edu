<?php
namespace app\service\controller;
use app\service\model\Courseware;

class CoursewareCon extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /*
     * 课件添加实现
     * @param 类型 参数名 参数说明
     */
    public function addCourseware() {
        $data = $this->data;
        if (@empty($data['upload_files'])) {
            return getBack(1, '课件为空');
        }
        $courseware = new Courseware();
        $return = $courseware->addWare($data);
        return $return;
    }
    /*
     * 课件查询实现
     * @param 类型 参数名 参数说明
     */
    public function queCourseware() {
        $data = $this->data;
        $courseware = new Courseware();
        $result = $courseware->queWare($data);
        if (!$result) {
            return getBack(0, '');
        } else {
            return getBack(0, $result);
        }
    }
    /*
     * 课件编辑实现
     * @param 类型 参数名 参数说明
     */
    public function upCourseware() {
        $data = $this->data;
    }
    /*
     * 课件删除实现
     * @param 类型 参数名 参数说明
     */
    public function delCourseware() {
        $data = $this->data;
        if (@empty($data['id'])) {
            return getBack(1, '参数错误！');
        }
        $courseware = model('Courseware');
        $result = $courseware->delWare1($data['id']);
        return $result;
    }

}