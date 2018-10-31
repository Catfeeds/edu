<?php
namespace app\admin\model;
use think\Model;
class Role extends Model {
    /**
     * 关联role,,access,node表查询数据
     * @param $id 角色id
     * @return false|mixed|\PDOStatement|string|\think\Collection  关联查询结果
     */
   public function searchPermission($id) {
       $res = db('role')
            ->alias('role')
            ->join('__ACCESS__ access','role.id = access.role_id')
            ->join('__NODE__ node','access.node_id = node.id')
            ->field('node_id')
            ->select($id);
       return $res;
   }
    /**
     * 处理表单数据，将nodeid和level组合的字符串分隔成数组并与
     * 角色id组合构造成可以直接存入数据库的键值对数组形式 $arr为最终数组
     * 配置权限之前先删除角色原有权限
     * @param $id 是角色id
     * @return int 1 配置权限成功  0 配置权限失败
     */
   public  function setPermissions($id) {
       $access = new Access;
       if ( !isset(input('post.')['access']) ) {
           $res = $access->del($id);
           if( $res !== false ) {
               return 1;
           } else {
               return 0;
           }
       } else {
           $access->del($id);
           $dat = input('post.')['access'];
           foreach ($dat as $k=>$v) {
               $arr[$k]['role_id'] = $id;
               $tt = explode(',',$v);
               $arr[$k]['node_id'] = $tt[0];
               $arr[$k]['level'] = (int)$tt[1];
           }
           $del = $access->insertAll($arr);
           if( $del !== false ) {
               return 1;
           } else {
               return 2;
           }
       }
   }

    /**
     * 删除角色时同时删除用户角色表数据、角色节点表数据
     * @param $id 角色id
     * @return int 1 删除成功 0 删除失败
     */
   public function delRoleAccess($id) {
       $roleuser = new RoleUser;
       $roleuser->del($id);
       $access = new Access;
       $access->del($id);
       $role = Role::get($id);
       $res = $role->delete();
       if ($res) {
           return 1;
       } else {
           return 0;
       }
   }
   public function giveRole($userid,$roleids) {
       $user = new User;
       $res = $user->where('id',$userid)->find();
       if($res) {
            $user =new User;
       }
   }
   /*
    * 根据用户id获取用户角色
   */
   public function getUserRole($uid) {
      $info = $this->alias('role')->join('__ROLE_USER__ roleuser', 'roleuser.role_id = role.id')->where(['roleuser.user_id' => $uid, 'role.status' => 1])->field('role.*')->find()->toArray();
      if ($info) {
        return $info;
      } else {
        return false;
      }
   }
   /*
    * 根据条件获取角色信息
   */
   public function getRoleInfo($where, $field = '*') {
      if (!isset($where['status'])) {
          $where['status'] = 1;
      }
      $info = collection($this->where($where)->field($field)->select())->toArray();
      if ($info) {
        return $info;
      } else {
        return false;
      }
   }
    /*获取用户角色级别
      返回值  int 1：管理员角色
             int2:教师角色
             int3：学生角色
    */
    public function getUserRoleLevel($uid = false) {
        if (!$uid) {
            return false;
        }
        $join = [
            ['__ROLE_USER__ ru', 'role.id = ru.role_id'],
            ['__USER__ u', 'u.id = ru.user_id']
        ];
        $where = ['u.status' => 0, 'role.status' => 1, 'u.id' => $uid];
        $field = 'role.level';
        $result = $this->alias('role')->where($where)->join($join)->field($field)->find();
        if (!$result) {
          return false;
        }
        if ($result->level < 4) {
          return 1;//管理员角色
        } else if ($result->level < 7) {
          return 2;//教师角色
        } else {
          return 3;//学生角色
        }
    }
}