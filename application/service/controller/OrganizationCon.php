<?php
namespace app\service\controller;
use app\service\model\Organization;
use app\service\model\Course;
use app\service\model\User;

class OrganizationCon extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /*
     * 机构添加实现
     * @param 类型 参数名 参数说明
     */
    public function addOrganization() {
        //前端上传的所有数据都在 $this->data 里面
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //添加的时候，注意父路径id和父节点数id，所有时间，均由time()函数得到
        $organization = new Organization();             
        $add = $organization->addMessage($data);  
        return $add;    
    }
    /*
     * 根据机构id获取机构信息
     * @param 类型 参数名 参数说明
     */
    public function getOrgInfo() {
        $data = $this->data;
        $Organization = model('Organization');
        if (!isset($data['id'])) {
            echo getBack(1, '参数错误！');
            exit;
        }
        $info = $Organization->getInfoFId($data['id']);  
        return getBack(0, $info);
    }
    /*
     * 机构编辑实现
     * @param 类型 参数名 参数说明
     */
    public function upOrganization() {
        //前端上传的所有数据都在 $this->data 里面
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $organization = new Organization();       
        $update = $organization->updateMessage($data);
        return $update;

    }
    /*
     * 机构查询实现
     * @param 类型 参数名 参数说明
     */
    public function queOrganization() {
        //前端上传的所有数据都在 $this->data 里面
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $organization = new Organization();
        $que = $organization->queMessageCheck($data);
        return $que;
        //var_dump($que);

    }
    /*
     * 根据机构id获取下级机构
     */
    public function queNextOrg() {
        $org = new Organization();
        $data = $org->queMessageChild_part($this->data['id'], 'id, name, haschild');
        return getBack(0, $data);
    }
    /*
     * 获取登陆用户的机构信息
     */
    public function getUserOrg() {
        $org = new Organization();
        $data = $org->getUserOrg($this->data);
        return getBack(0, $data);
    }
    /*
     * 获取用户所在的机构及其所有的子机构(默认获取登录用户的机构)
     */
    public function getUserOrgAll() {
        $data = $this->data;
        $org = new Organization();
        if (isset($data['id']) && empty($data['id'])) {
            $u_org_id = $data['id'];
        } else {
            $user = new User();
            $u_org_id = $user->getOrgInfo($data['uid']);
        }
        $re_data = $org->getChildren($u_org_id, true);
        return getBack(0, $re_data);
    }
    /*
     * 机构删除实现
     * @param 类型 参数名 参数说明
     */
    public function delOrganization() {
        //前端上传的所有数据都在 $this->data 里面
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //统一删除使用修改status字段
        $organization = new Organization();
        $del = $organization->delMessage($data);
        return $del ? getBack(0, '删除成功！') : getBack(1, '删除失败！'); 
    }
    /*
     * 通过机构查询指定机构下的课程
     * @param 类型 参数名 参数说明
     */
    public function queCourse() {
        $data = $this->data;
        $course = new Course();
        $where = empty($data['organization']) ? [] : ['department' => $data['organization']];
        $course_info = $course->queAllOrPage($data['uid'], empty($data['page']) ? '' : $data['page'], empty($data['rows']) ? '' : $data['rows'], $where);
        if (empty($data['page'])) {
            return getBack(0, $course_info);
        } else {
            $re_data['page'] = $course_info['last_page'];
            $re_data['data'] = $course_info['data'];
            return getBack(0, $re_data);
        }
    }
    

}