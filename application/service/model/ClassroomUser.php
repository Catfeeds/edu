<?php
namespace app\service\model;
use think\Model;

class ClassroomUser extends Model
{
    protected function initialize() {
        parent::initialize();
    }
    protected $pk = 'id';

    //课程新增id自动完成
    protected $auto = ['id'];

    protected function setIdAttr($val) {
        return $val ? $val : createGuid();
    }
    /*
     *方法详情
     *@param 参数类型 参数名  参数用途 
     */
    public function text() {
        
    }
    /**@author  叶文
     * 通过学号查询用户信息
     * @param $idnumber 学号
     */
    public function userInfo($idnumber) {
        $user = new User;
        $organization = new Organization;
        $info = $user->where('idnumber',$idnumber)->field('id,realname,idnumber,pid,picture,description,profession,enter_time')->find();
        if($info) {
            $result = $organization->where(['id' => $info['pid'], 'status' => 0])->field('name')->find();
            $info['pid'] = $result['name'];
        }
        return $info;
    }
    /**@author  叶文
     * 通过id查询用户信息
     * @param $userid 用户id
     * @return array|false|\PDOStatement|string|Model
     */
    public function id_userInfo($userid) {
        $user = new User;
        $organization = new Organization;
        $info = $user->where('id',$userid)->field('id,realname,idnumber,pid,picture,description,profession,enter_time')->find();
        if($info) {
            $result = $organization->where(['id'=>$info['pid'],'status'=>0])->field('name')->find();
            $info['pid'] = $result['name'];
            return $info;
        }
    }
    /**@author  叶文
     * 通过课堂id获取参与课堂的人员信息
     * @param $id 课堂id
     */
    public function ClassmateInfo($id, $configPage = false, $search = false) {
        $where = ['cu.classroom_id' => $id, 'user.status' => 0];
        if ($search) {
            $where['user.realname|user.idnumber'] = ['like', $search];
        }
        $join = [
            ['__USER__ user', 'cu.user_id = user.id'],
            ['__CLASS__ class', 'class.id = user.class', 'LEFT']
        ];
        $field = 'cu.role, user.id, user.realname as name, user.idnumber as number, class.name as class';
        if (!$configPage) {
            $info = collection($this->alias('cu')->where($where)->join($join)->field($field)->select())->toArray();
        } else {
            $info = $this->alias('cu')->where($where)->join($join)->field($field)->paginate($configPage, config('paginate.custom'))->toArray();
        }
        return $info;
    }
    /*
     *方法详情
     *删除人员
     *@param 参数类型 参数名  参数用途 
     *@param char(32) user_id或classroom_id 定位需要删除的信息
     */
    public function delClassroomUser($data) {
        if(isset($data['user_id'])){
            $num = count($data['user_id']);
            for($i = 0; $i < $num; $i++){
                $res = $this->where('user_id',$data['user_id'][$i])->delete();
            }
        }else{
            $res = $this->where('classroom_id',$data['id'])->delete();
        }
        return $res;
    }

}