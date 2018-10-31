<?php
namespace app\service\model;
use think\Model;
use think\Loader;
class Classes extends Model
{
    protected $pk = 'id';
    protected $auto = ['id'];
    protected $table = 'edu_class';
    protected function setIdAttr($val) {
        return $val ? $val : createGuid();
    }
    /**添加班级
     * @author 叶文
     * @param $data 班级信息
     * @return string 0 成功 1 失败
     */
    public function addClass($data) {
        $validate = Loader::validate('Classes');
        if($validate->scene('add')->check($data)){
            $res = $this->classValidate($data);
            if(!$res) {
                $data['timecreated'] = time();
                $data['status'] = 0;
                $res = $this->allowField(true)->save($data);
                if ($res){
                    return $this->id;
                } else {
                    return false;
                }
            }else {
                return false;
            }
        } else{
            echo getBack(1,$validate->getError());
            exit;
        }
    }
    /**删除班级
     * @author叶文
     * @param $ids 班级id数组
     * @return string 0 成功 1 失败
     */
    public function delClass($ids) {
        $user = new User();
        foreach ($ids as $k=>$v){
            $arr[] = ['status' => 1, 'timemodified' => time(), 'id' => $v];
        }
        $res = $this->saveAll($arr);
        if($res !== false) {
            $user->where('class', 'in', $ids)->update(['class' => null]);
            return getBack(0, '删除班级成功');
        }else {
            return getBack(1, '删除班级失败');
        }
    }
    /**班级编辑
     * @author 叶文
     * @param $data 课程信息
     * @return string 0 成功 1 失败
     */
    public function upClass($data) {
        $data['timemodified'] = time();
        $result = $this->validate('Classes.edit')->field('name, describe, timemodified')->isUpdate(true)->save($data);
        if ($result === false) {
            echo getBack(1, $this->getError());
            exit;
        }
        return true;
    }
    /**通过班级id查询班级信息
     * @author 叶文
     * @param $id 班级id
     * @return string 班级信息
     */
    public function aqueClass($id) {
        $classes = new Classes;
        $oranization = new Organization;
        $res = $classes->where('status',0)->find($id);
        if ($res){
            $department=$oranization->field('name')->where('status',0)->find($res['department']);
            if($department) {
                $res['department'] = $department['name'];
            }
            return getBack(0, $res);
        }else{
            return getBack(1, '班级不存在');
        }
    }
    /**通过机构id查询该机构下的所有有效班级
     * @param array $data ['uid' => '登陆用户id', 'id' => '机构id']
     */
    public function queClassForOrg($data) {
        if (isset($data['id'])) {
            $org_id = $data['id'];
        } else {
            $user = new User();
            $org_id = $user->getOrgInfo($data['uid']);
            if (empty($org_id)) {
                echo getBack(1, '错误，请重新登录！');
                exit;
            }
        }
        $result = collection($this->alias('class')->where(['class.status' => 0, 'org.status' => 0, 'class.department' => $org_id])->join('__ORGANIZATION__ org', 'org.id = class.department')->field('class.id, class.name, class.code, class.describe, class.number')->order('class.timecreated')->select())->toArray();
        if (!$result) {
            return false;
        }
        return $result;
    }

    /**获取当前机构的下一级机构和下一级机构的所有班级
     * @param $id 机构id
     * @return string
     */
    public function classesOrg($id,$uid) {
        $organization = new Organization;
        $user = new User;
        /*用户所属机构id*/
        $uorgid = $user->where('status',0)->field('pid')->find($uid);
        if($uorgid) {
            $uorgid = $uorgid['pid'];
            $name = $organization->getProtionOrg($uorgid, $id);
            if($name) {
                foreach ($name as $v) {
                    $names[] = $v['name'];
                }
            }
        }else {
            $names = null;
        }
        if($uorgid) {
            $uorgname = $organization->where('status',0)->find($uorgid);
            $uorgname = $uorgname['name'];
        }else {
            $uorgname = null;
        }
        /*当前机构id班级*/
        $class = db('class')
            ->alias('class')
            ->join('__ORGANIZATION__ organization','class.department = organization.id')
            ->field('class.*, organization.id as orgid, organization.name as dname')
            ->where('class.status',0)
            ->where('organization.status',0)
            ->where('department',$id)
            ->select();
        if($class) {
            foreach ($class as $k=>$v) {
                $trr[$k] = $v;
                $trr[$k]['department'] = array($uorgname, $names, $v['dname']);
            }
        }else {
            $trr = null;
        }
        $res['class'] = $trr;
        $ids = $organization->nextOrg($id);
        if($ids) {
            $result = $organization->where('id','in',$ids)->select();
        }else {
            $result =null;
        }
        $res['result'] = $result;
        return getBack(0, $res);
    }
    /**获得当前用户所在机构及其子机构所有班级
     * @author 叶文
     * @param $id
     * @return string
     */
    public function cqueClass($id) {
        $user = new User;
        $res = $user->field('pid')->where('status',0)->find($id);
        if($res) {
            return $this->bqueClass($res['pid']);
        }else {
            return getBack(1, '系统错误，请与管理员联系');
        }
    }
    /**查询某班级下所有人员
     * @author 叶文
     * @param $id 班级id
     * @return string
     */
    public function queClassmate($where, $rows, $page = 1) {
        $configPage['list_rows'] = $rows ? $rows : config('paginate.list_rows');
        $configPage[config('paginate.var_page')] = $page;

        $join = [
            ['__USER__ user', 'user.class = c.id']
        ];
        $data = $this->alias('c')->join($join)->where($where)->field('user.id, user.username, user.realname, user.email, user.idnumber, user.enter_time, user.logintime')->paginate($configPage, config('paginate.custom'))->toArray();

        if($data) {
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]['enter_time'] = date('Y-m-d', $value['enter_time']);
                $data['data'][$key]['logintime'] = date('Y-m-d H:i:s', $value['logintime']);
            }
            return $data;
        } else{
            return false;
        }
    }
    /**删除班级下人员
     * @author 叶文
     * @param $ids 要删除的用户id数组
     * @return string
     * &bug 存在一个问题，如果前端传过来的用户id并不属于该班级，会出现错误
     */
    public function delClassmate($ids,$classid) {
        $user = new User;
        $classes = new Classes;
        foreach ($ids as $k=>$v){
            $arr[$k]['class'] = null;
            $arr[$k]['timemodified'] = time();
            $arr[$k]['id'] = $v;
        }
        $number = $classes->where('status',0)->find($classid);
        if($number){
            $num = count($arr);
            $count = $number['number'] - $num;
            if($count>=0) {
                $res = $user->saveAll($arr);
                if($res) {
                    $classes->save(['number'=>$count],['id'=>$classid]);
                    return getBack(0, '删除成功');
                }else {
                    return getBack(1, '删除失败');
                }
            } else {
                return getBack(1, '系统错误，请于管理员联系');
            }
        } else {
            return getBack(1, '系统错误，班级不存在，请于管理员联系');
        }
    }

    /**班级信息数据验证 同一机构班级名或班级号不能相同
     * @author 叶文
     * @param $date 修改或者编辑班级信息数据
     * @return string
     */
    public function classValidate($data) {
        $result = $this->where(['department' => $data['department'], 'code' => $data['code']])->find();
        if ($result) {
            echo getBack(1, '班级编号重复！');
            exit;
        } else {
            return false;
        }
    }
    /**
     * 万能查询:根据WHERE查询
     * @author 宋玉琪
     * @param where 前端传入的查询条件
     */
    public function searchByWhere($where, $str = "*")
    {
        isset($where['status']) ? '' : $where['status'] = 0;
        $res = $this->where($where)->field($str)->select();
        if ($res) {
            return collection($res)->toArray();
        } else {
            return false;
        }
    }
}