<?php
namespace app\service\controller;
use app\service\model\User;
use app\admin\model\RoleUser;
use app\admin\model\Role;
use app\service\model\ProfessionUser;

class UserCon extends Common
{
    public function __construct(){
        parent::__construct();
    }
    /*
     * 得到用户列表
     */
/*    public function getUserList() {
        $data = $this->data;
        $user = new User();
        $re_data = $user->getUserList($data);
        return $re_data;
    }*/
    public function getUserList() {
        $data = $this->data;
        $User = model('User');
        $where = [];
        if (isset($data['idnumber'])) {
            $where['user.idnumber'] = ['like', '%' . $data['idnumber'] . '%'];
        }
        if (isset($data['username'])) {
            $where['user.username'] = ['like', '%' . $data['username'] . '%'];
        }
        if (isset($data['realname'])) {
            $where['user.realname'] = ['like', '%' . $data['realname'] . '%'];
        }
        if (isset($data['orgid'])) {
            $Organization = model('Organization');
            $org_all = $Organization->getChildren($data['orgid']);
            $where['user.pid'] = ['in', $org_all];
        }
        $page = 1;
        if (isset($data['page']) && (int)$data['page'] > 0) {
            $page = $data['page'];
        }
        $rows = config('paginate.list_rows');
        if (isset($data['rows']) && (int)$data['rows'] > 0) {
            $rows = $data['rows'];
        }

        $re_data = $User->getUserList2($where, $page, $rows, $data);
        if ($re_data) {
            return getBack(0, $re_data);
        } else {
            return getBack(1, '请求数据失败！');
        }

    }
    /**@author  李桐
     *用户新增
     */
     public function addUser(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $uid = $user->addUser($data);
        if ($uid) {
            if (@!empty($data['role'])) {
                $role = new Role();
                if ($role->where(['id' => $data['role'], 'status' => 1])->find()) {
                    $roleUser = new RoleUser();
                    $roleUser->data(['role_id' => $data['role'], 'user_id' => $uid])->save();
                }
            }
            if (@!empty($data['professional'])) {
                $profession = model('Profession');
                if ($profession->where(['id' => $data['professional'], 'status' => 0])->find()) {
                    if (!is_array($data['professional'])) {
                        $data['professional'] = explode(',', $data['professional']);
                    }
                    $pro = new ProfessionUser();
                    $pro->addForUser($data['professional'], $uid);
                }
            }
        }
        return getBack(0, '添加成功！');
     }
    /**@author  李桐
     *用户删除
     */
    public function delUser(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->delUser($data);
        return $result;
    }
    /**@author  李桐
     *用户修改
     */
    public function upUser(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->upUser($data);
        return $result;
    }
    /*
     * 根据用户id获取用户详情
     */
    public function getUserInfoId() {
        $data = $this->data;
        $user = new User();
        $re_data = $user->getUserInfoId($data);
        return getBack(0, $re_data);
    }
    /**@author  李桐
     *用户查询
     *通过单个Id来查询
     */
    public function queIdUser(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->queIdUser($data);
        return $result;
    }
    /**@author  李桐
     *用户查询
     *通过用户名模糊查询
     */
    public function queUserName(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->queUserName($data);
        return $result;
    }
    /**@author  李桐
     *用户查询
     *通过学号查询
     */
    public function queIdNumber(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->queIdNumber($data);
        return $result;
    }
    /**@author  李桐
     *用户查询
     *通过班级查询
     */
    public function queClass(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->queClass($data);
        return $result;
    }
    /**@author  李桐
     *用户查询
     *通过专业查询
     */
    public function queProfession(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->queProfession($data);
        return $result;
    }
    /**@author  李桐
     *已分配/未分配
     *通过查询数据库中班级和专业字段是否有信息（学生）
     */
    public function distribution(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->distribution($data);
        return $result;
    }
    /**@author  李桐
     *查询所有学生、老师、管理员
     *@param string member（member = student/teacher/admin）
     */
    public function queMember(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->queMember($data);
        return $result;
    }
    /**@author  李桐
     *用户查询
     *根据机构来查询
     */
    public function queOrg(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $user = new User();
        $result = $user->queOrg($data);
        return $result;
    }
    /**@author 贾芸吉
     * 用户批量新增
     * 用用户
     */
    public  function  InsertExcel(){
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存sql日志.sql
        $user = new User();
        $result = $user->Insertlist($data);
        return $result;
    }

}
