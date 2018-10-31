<?php
namespace app\service\controller;
use app\service\model\Area;
use app\service\model\Course;
use app\service\model\Organization;
use app\service\model\CourseCategories;
use app\service\model\User;
use app\admin\controller\Rbac;

class DataRequest extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /*
     * 添加课程时，数据请求
     * @param $data array 必须包含page值，表示请求第几页的课程数据
     */
    public function courseRequest() {
        $data = $this->data;
        $param_name = ['page', 'rows'];
        foreach ($param_name as $v) {
            if (!array_key_exists($v, $data)) {
                echo getBack(1, '丢失必要信息，请重试！');
                exit;
            }
        }
        $course = new Course();
        $organization = new Organization();
        $categories = new CourseCategories();
        $user = new User();
        $u_org_id = $user->getOrgInfo($data['uid']);
        $course_info = $course->queAllOrPage($data['uid'], $data['page'], $data['rows']);//用户id，页数，行数
        $return_data['course'] = $course_info['data'];
        $return_data['categories'] = $categories->queAll($u_org_id, 'id, name');
        $return_data['organization'] = $organization->getChildren($u_org_id, true);
        $return_data['page'] = $course_info['page'];
        return getBack(0, $return_data);
    }
/*    public function search() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $area = new Area();               
        $que = $area->searchTo($data);
        return $que;
    }*/
    /*
     *
     */
    public function crRequest() {
        $data = $this->data;
        $org = new Organization();
        $cate = new CourseCategories();
        $user = new User();
        $user_pid = $user->getOrgInfo($data['uid']);
        $org_user_all = $org->getChildren($user_pid, true, true);//得到所有机构的id和信息
        $cate_all = $cate->where('visible', 0)->where('department', 'in', $org_user_all['id'])->field('id, name')->select();
        return getBack(0, ['categories' => $cate_all, 'organizations' => $org_user_all['info']]);

    }

    //获取角色信息
    public function getSonRole() {
        $data = $this->data;
        $auth = $this->createAuth();
        $rbac = new Rbac($auth);
        $id = $data['uid'];
        if (@!empty($data['id'])) {
            $id = $data['id'];
        }
        $info = $rbac->getSonRole($id);
        if ($info) {
            return $info;
        }
        return getBack(1, '查询失败！');
    }

}