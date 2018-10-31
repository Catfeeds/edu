<?php
namespace app\service\model;
use think\Model;

class ProfessionUser extends Model
{

    /**@author  李桐
     *方法详情(传入的数据格式['profession'=>'proid','user'=>['id1','id2']]或者['profession'=>['pro1','pro2'],'user=>'id''])
     *新增
     *@param 参数类型 参数名  参数用途 
     *@param char(32) profession 需要保存的信息
     *@param char(32) userId    需要保存的信息
     */
    public function add($data){
        /*  测试数据
        $data['user'] = ['B608AA535E0EC9C5A7BC8548AF39D4CE','B608AA535E0EC9C5A7BC8548AF39D4CE','B608AA535E0EC9C5A7BC8548AF39D4CE'];
        $data['profession'] = ['B608AA535E0EC9C5A7BC8548AF39D4CQ'];*/
        $numPro = count($data['pro']);
        if($numPro != 1){
            for($i = 0; $i < $numPro; $i++){
                $list[$i] = (
                     ['user' => $data['user'][0], 'profession' => $data['profession'][$i]]);
            }
        }else{
            $profession = $data['pro'];
            for($i = 0; $i < count($data['user']); $i++){
                $list[$i] = (
                     ['user' => $data['user'][$i], 'profession' => $data['profession'][0]]);
            }
        }
        $res = $this->saveAll($list);
        if($res){
            return getBack(0,"保存成功");
        }else{
            return getBack(1,"保存失败");
        }
    }
    /**@author  李桐
     *方法详情
     *修改
     *@param 参数类型 参数名  参数用途 
     *@param char(32) profession 需要修改的信息
     *@param char(32) userId    定位修改信息的位置
     */
    public function up($data){
        $res = $this->save(['profession' => $data['profession']],['user' => $data['id']]);
        return $res;
    }
    /**@author  李桐
     *方法详情
     *查询
     *@param 参数类型 参数名  参数用途 
     *@param char(32) profession 需要查询的信息
     */
    public function que($data){
        $res = $this->where('profession',$data['profession'])->select();
        return $res;
    }
    /**@author  李桐
     *方法详情
     *查询（通过用户id查询）通过用户id实现in查询
     *@param 参数类型 参数名  参数用途 
     *@param char(32) user 需要查询的信息
     */
    public function queId($user){
        $res = $this->where('user','in',$user)->select();
        return $res;
    }
    /*
     *单用户，多专业添加
     *@param array $data 专业id数组（一：按sign顺序的专业id数组）
     *                   一：['第一专业id', '第二专业id']
     *@param string $uid 用户id
     *@param bool $pattern $data的数据格式，默认为第一种
     */
    public function addForUser($data, $uid) {
        $sign = 1;
        foreach($data as $val) {
            $pro_data[] = ['profession' => $val, 'user' => $uid, 'sign' => $sign];
            $sign++;
        }
        $result = $this->saveAll($pro_data);
        if (!$result) {
            echo getBack(1, '保存专业失败！');
            exit;
        }
    }
    /**
     * 根据专业id，获取除该专业下面用户的其它成员
     * @param id    专业id
     * @param row   每页显示条数
     * @param page  显示第几页
     */
     public function getOtherUser($id, $page, $rows, $data = false) {
        $pro_user = collection($this->alias('prouser')->join('__PROFESSION__ pro', 'pro.id = prouser.profession')->where(['pro.status' => 0, 'prouser.profession' => $id])->field('prouser.user')->select())->toArray();
        $not_in = [];
        foreach ($pro_user as $value) {
            $not_in[] = $value['user'];
        }
        $User = new User();
        $join = [
            ['__ROLE_USER__ roleuser', 'roleuser.user_id = user.id'],
            ['__ROLE__ role', 'role.id = roleuser.role_id and role.name = "student"']
        ];
        $where = ['user.status' => 0, 'user.id' => ['not in', $not_in]];
        $field = 'user.id, user.username, user.realname, user.idnumber';
        $configPage['list_rows'] = $rows ? $rows : config('paginate.list_rows');
        $configPage[config('paginate.var_page')] = $page ? $page : 1;
        $info = $User->alias('user')->join($join)->where($where)->field($field)->paginate($configPage, config('paginate.custom'))->toArray();
        if ($info) {
            return $info;
        } else {
            return false;
        }
     }
    /**
     * 批量添加专业下成员信息(前端还未给数组的键)
     * @param pro_id  专业id
     * @param id      用户id
     * @param sign    专业排序[1.2.3]
     */
    public function addManyProfessionPerson($data)
    {
        foreach ($data['user'] as $v) {
            $count = $this->where('user', $v)->count();
            $list[] = ['profession' => $data['id'],'user' => $v, 'sign' => $count++];
        }
        $this->saveAll($list);
        return true;
    }
    /**
     * 删除专业成员关系:在专业用户表中删除对应数据  格式： 'pro_id' => '专业id','user_id' => ['id1', 'id2']      
     * @author 陈博文
     * @param  id   前端传入的用户id
     */
    public function delWhere($where)
    {
        $info = $this->where($where)->delete();
        if($info != false){
            return true;
        }else{
            return false;
        }
    }

}