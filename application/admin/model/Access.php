<?php
namespace app\admin\model;
use think\Model;
class Access extends Model {
    /**
     * @param $id 角色id
     */
    public function del($id) {
        $this->where('role_id', $id)->delete();
    }
}