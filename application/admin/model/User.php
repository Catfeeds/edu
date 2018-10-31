<?php
namespace app\admin\model;
use think\Model;
class User extends Model {
    /**
     * 获取所有用户列表
     * @return false|static[]  所有用户数组
     */
/*    public function personList() {
        $res = $this->select();
        if ($res) {
            foreach ($res as $k => $v) {
                $arr[$k]['userid'] = $v['id'];
            }
            foreach ($arr as $v) {
                $data[] = ($v['userid']);
            }
            $list = User::all($data);
            return $list;
        }
    }*/
    /**
     * 获取用户所有角色列表
     * @return false|mixed|\PDOStatement|string|\think\Collection  用户所有角色数组
     */
    public function userRole() {
        $res = $this->select();
        if ($res) {
            foreach ($res as $k => $v) {
                $arr[$k]['userid'] = $v['id'];
            }
            foreach ($arr as $v) {
                $data[] = $v['userid'];
            }
            $roles = db('user')
                ->alias('user')
                ->join('__ROLE_USER__ roleuser', 'user.id=roleuser.user_id')
                ->join('__ROLE__ role', 'roleuser.role_id=role.id')
                ->field('user.id,name')
                ->select();
            return $roles;
        }
    }
    /**
     * 删除用户原有角色，并从新添加角色
     * @param $userid   用户id
     * @return int   0 删除原有角色，添加角色失败 1 删除原有角色，添加角色成功 3 删除原有角色
     */
    public function giveRole($userid) {
        $roleuser = new RoleUser;
        if ( !isset(input('post.')['role_id']) ) {
            $res = $roleuser->where('user_id','in',$userid)->delete();
            if ($res !==false) {
                return 3;
            }else{
                return 0;
            }
        } else {
            $roles = input('post.')['role_id'];
            $roleuser->where('user_id','in',$userid)->delete();
            foreach ($userid as $k1 => $v1) {
                foreach ($roles as $k2 => $v2) {
                    $arr[$k1][$k2]['user_id'] = $v1;
                    $arr[$k1][$k2]['role_id'] = $v2;
                }
            }
            foreach ($arr as $v){
                foreach ($v as $v2){
                    $trr[] =$v2;
                }
            }
            $res = $roleuser->insertAll($trr);
            if ($res) {
               return 1;
            } else {
                return 0;
            }
        }
    }
}
