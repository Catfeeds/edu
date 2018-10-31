<?php
namespace app\service\controller;
use app\service\model\Attachments;
use app\service\model\Course;
use app\service\model\Organization;
use app\service\model\User;
use think\Loader;
class CourseCon extends Common
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
    public function addCourse() {
        //目前该if每个方法必须使用
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $course = new Course;
        //对上传图片和课程信息检查
        $course->checkData(['coursepic' => @empty($data['upload_files']) ? '' : $data['upload_files'][0]['file']], $data);
        //判断全名和简称是否重合
        $course->checkName($data);
        return $course ->saveCourse($data);
    }
    /*
     * 课程编辑实现
     * @param 类型 参数名 参数说明
     */
    public function upCourse() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $course = new Course;
        $course->checkData(['coursepic' => empty($data['upload_files']) ? '' : $data['upload_files'][0]['file']], $data, 'update');
        //判断全名和简称是否重合
        $course->checkName($data, 'update');
        return $course ->updataCourse($data);
    }
    /*
     * 课程查询实现    查询某用户下的所有课程
     * @param 类型 参数名 参数说明
     */
    public function queCourse() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $course = new Course;
        $course_info = $course ->queAllOrPage($data['uid']);
        return getBack(0, $course_info);
    }
    /*
     * 根据指定机构请求课程
     */
    public function queCourseOrg() {
        $data = $this->data;
        $course = new Course();
        $user = new User();
        $data['page'] = is_numeric($data['page']) ? ($data['page'] < 1 ? 1 : $data['page']) : 1;
        $data['rows'] = is_numeric($data['rows']) ? ($data['rows'] < 1 ? config('paginate.list_rows') : $data['rows']) : config('paginate.list_rows');
        $where['department'] = empty($data['organization']) ? $user->getOrgInfo($data['uid']) : $data['organization'];
        $course_info = $course ->queAllOrPage($data['uid'], $data['page'], $data['rows'], $where);
        return getBack(0, ['data' => $course_info]);
    }
    /*
     * 根据指定页数请求课程
     */
    public function queCoursePage() {
        $data = $this->data;
        $course = new Course();
        $data['page'] = is_numeric($data['page']) ? ($data['page'] < 1 ? 1 : $data['page']) : 1;
        $data['rows'] = is_numeric($data['rows']) ? ($data['rows'] < 1 ? config('paginate.list_rows') : $data['rows']) : config('paginate.list_rows');
        $where = empty($data['organization']) ? [] : ['department' => $data['organization']];
        $course_info = $course ->queAllOrPage($data['uid'], $data['page'], $data['rows'], $where);
        return getBack(0, ['data' => $course_info]);
    }
    /*
     * 课堂获取课程信息方法
     * @param array data ['uid' => 用户id, 'page' => 请求页面, 'rows' => 每页显示条数]
     */
    public function queCourseFc() {
        $data = $this->data;
        $course = new Course();
        $data['page'] = is_numeric($data['page']) ? ($data['page'] < 1 ? 1 : $data['page']) : 1;
        $data['rows'] = is_numeric($data['rows']) ? ($data['rows'] < 1 ? config('paginate.list_rows') : $data['rows']) : config('paginate.list_rows');
        return $course->getCourseFc($data);
    }

    /*
     * 课程删除实现
     * @param 类型 参数名 参数说明
     */
    public function delCourse() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //通过$data['del_type']的值和用户角色判断删除方式  一般的修改status值，最高权限直接从数据表删除
        $course = new Course;
        return $course->delCourse($data['id']);
    }
    /*
     * 查询本级课程，层级
     * @id  主键  课程id  查询当前课程
     * @uid  整型  用户id  查询机构课程
     */
    public function queConrseSelf() {
        $course = new Course();
        $result = $course->queSelf($this->data);
        return $result;
    }
    /*
     * 根据课程id得到课程信息
     */
    public function getCourseInfo() {
        $data = $this->data;
        $course = new Course();
        $re_data = $course->getInfo($data['course']);
        return $re_data ? getBack(0, $re_data) : getBack(0, '无该课程信息！');
    }

}