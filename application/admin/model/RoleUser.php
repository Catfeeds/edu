<?php
namespace app\admin\model;
use think\Model;
class RoleUser extends Model {
    public function  del($id) {
        $this->where('role_id', $id)->delete();
    }
    //关联查询
    public function getUserRoleInfo($id) {
        $info = $this->where(['user_id' => $id])->alias('ru')->join('__ROLE__ r ', 'r.id = ru.role_id')->find();
        return $info;
    }

}