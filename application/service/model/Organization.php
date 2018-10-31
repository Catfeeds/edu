<?php
namespace app\service\model;
use think\Model;

class Organization extends Model
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
    protected $readonly = ['id', 'org_code', 'pid', 'parentstr', 'timecreated', 'area'];
    /*
     *                    机构添加 
     *      
     *@param varchar area         保存前端传入的机构所属地区
     *@param char    pid          保存前端传入的父路径ID
     *@param char    org_code     保存前端传入的编号
     *@param tinyint haschild     记录是否存在下一级
     *@param int     name         保存前端传入的机构名称
     *@param text    address      保存前端传入的详细地址
     *@param varchar type         保存前端传入的机构类型
     *@param text    summary      保存前端传入的机构简介
     */
    public function addMessage($data) {
        $organization = new Organization($data);
        $connect = empty($data['pid']) ? '' : $organization->where('id', $data['pid'])->find();
        if($connect) {
          $organization->parentstr = $connect['parentstr'] . $connect['id'] . ",";
          $organization->haschild = '1';

        }else {
            $organization->parentstr = config('parentstr_prefix');
        }
        $organization->timecreated = time();
        $organization->timemodified = '0';
        $organization->haschild = '0';
        $organization->level = $connect['level']+1;
        $res = $organization->allowField(true)->save();
        $organization->where('id',$data['pid'])->update(['haschild' => 1]);
        if($res) {
          return getBack(0, $res);
        }else {
          return getBack(1, 'Error');
        }
    }
    /*
     *                    机构更新
     /*
     *@param varchar area         保存前端传入的机构所属地区
     *@param char    org_code     保存前端传入的编号
     *@param tinyint haschild     记录是否存在下一级
     *@param int     name         保存前端传入的机构名称
     *@param text    address      保存前端传入的详细地址
     *@param varchar type         保存前端传入的机构类型
     *@param text    summary      保存前端传入的机构简介
     */
    public function updateMessage($data) {
        $organization = new Organization();
        $organization->timemodified = time();
        $res = $organization->allowField(true)->save($data,['id'=>$data['id']]);
        if($res) {
            return getBack(0 , '编辑成功！');
        }
        else {
            return getBack(1 , 'Error');
        }
    }
    /*
     *                机构查询
     *@param  string all  前端传入的一个值为all或者top或者id时，分别进入不同的方法
     */
    public function queMessageCheck($data) {
        if($data['type'] == 'all') {
            $all = $this->queMessageNone_All($data);
            return getBack(0,$all);
        } else if($data['type'] == 'top') {
            $top = $this->queMessageNone_top($data);
            return getBack(0,$top);
        } else if($data['type'] == 'id') {
            if($data['id']) {
                $child = $this->queMessageChild_part($data['id']);
                return getBack(0,$child);
            }
        }
    }
    /*
     *     查询所有有效机构
     *@param tinyint status   寻找状态为0的所有字段并返回每条信息的id,org_code,haschild,name,area,address,type,summary字段的内容
     */
    public function queMessageNone_All($data = null) {
        $organization = new Organization();
        $res = collection($organization->where('status', 0)->field('id,org_code,haschild,name,area,address,type,summary')->select())->toArray();
        return $res;
    }
    /*
     * 查询所有顶级机构
     *@param tinyint status   显示当前信息状态(0-正常 1-暂停)
     *@param char    pid      通过pid等于0来寻找最高级的信息,然后返回给前端最高级的id和name  
     */
    public function queMessageNone_top($data = null) {
        $organization = new Organization();
        $res = collection($organization->where('status', 0)->where('pid', 0)->field('id,name')->select())->toArray();
        return $res;
    }
    /*
     * 查询指定机构的下一层子机构
     *@param tinyint status   显示当前信息状态(0-正常 1-暂停)
     *@param char    id       前端传入的id用于在表中寻找对应的pid
     *@param char    pid      通过前端传入的id和表中的pid的值相等来寻找对应的信息，然后返回给前端寻找到的每条信息的id和name  
     */
     public function queMessageChild_part($id, $str = 'id, name') {
        $organization = new Organization();
        $res = $organization->where('status', 0)->where('pid', $id)->field($str)->select();
        return $res;
    }
    /*
     * 根据机构id得到机构信息
     */
    public function getInfoFId($id, $str = '*') {
        $info = $this->where(['status' => 0, 'id' => $id])->field($str)->find()->toArray();
        return $info ? $info : false;
    }
    /*
     * 获取登陆用户所在的机构信息
     * return 默认机构的id，name，haschild
     */
    public function getUserOrg($data) {
        $user = new User();
        $u_org_id = $user->getOrgInfo($data['uid']);
        $org_info = $this->getInfoFId($u_org_id, 'id, name, haschild');
        if (!$org_info) {
            echo getBack(1, '当前用户无机构');
        }
        return $org_info;
    }
    /*
     *                机构删除
     *@param  array data     前端传入的id用于确定删除的id,并jiang对应信息的status值变为1的状态
     */
    public function delMessage($data) {
        $user = new User();
        $u_org_id = $user->getOrgInfo($data['uid']);
        $u_org_info = $this->getInfoFId($u_org_id, 'parentstr');

        if(is_array($data['ids'])) {
            $is_true = true;
            foreach ($data['ids'] as $val) {
                 $is_true = $this->delOrg($val, $u_org_info['parentstr']);
                 if (!$is_true) {
                     return false;
                 }
            }
            return true;
        } else if (is_string($data['ids'])) {
            return $this->delOrg($data['ids'], $u_org_info['parentstr']);
        } else {
            echo getBack(1, '传递数据错误');
            exit;
        }
    }
    /*
     * 删除机构以及该机构下的所有子机构
     * @param string $id 机构id
     */
    public function delOrg($id, $u_parentstr){
        if (!is_string($id)) {
            echo getBack(1, '传入参数错误！');
            exit;
        }
        $strstr = $this->where(['status' => 0, 'id' => $id])->value('parentstr');
        if (strpos($strstr, $u_parentstr) === false) {
            echo getBack(1, '该用户无权限操作该机构');
            exit;
        }
        $son_strstr = $strstr . $id;
        //加事物
        //删除本机构
        $istrue = $this->where('id', $id)->update(['status' => 1, 'timemodified' => time()]);
        if ($istrue) {
            //删除子机构
            $this->where('parentstr', 'like', $son_strstr . '%')->update(['status' => 1, 'timemodified' => time()]);
            return true;
        } else {
            return false;
        }
    }
    /*
     * 获取当前机构及其子节点数组,根据需求得到不同的数据
     * @param $organizationid 机构id
     * @param bool $id true:返回所有子机构层级关系；false：返回所有子机构一维id数组
     * @param bool $all true:返回层级并且id数组；false：根据$id的值决定返回层级所有数据或者返回一维id信息
     * @return array 当前机构子节点数组
     */
    public function getChildren($organizationid, $id = false, $all = false) {
        $join = [['__AREA__ area', 'area.code = org.area']];
        $where = ['org.status' => 0, 'area.status' => 1];
        $field = 'org.id, org.pid, org.name, area.code, area.name as coude_name';
        $organizations = collection($this->alias('org')->join($join)->where($where)->field($field)->select())->toArray();//获取id，pid，机构名
        if ($all) {
            $arr_id = getClassify($organizations, 'id', 'pid', $organizationid, false);
            $arr_info = getClassify($organizations, 'id', 'pid', $organizationid, true);
             if($arr_id) {
                array_unshift($arr_id,$organizationid);
            } else {
                $arr_id[] = $organizationid;
            }
            foreach ($organizations as $val) {
                if($organizationid == $val['id']) {
                    $re_info = $val;
                    break;
                }
            }
            $re_info[config('classify_name')] = $arr_info[config('classify_name')];
            return ['id' => $arr_id, 'info' => $re_info];
        } else if (!$id) {
            $arr = getClassify($organizations, 'id', 'pid', $organizationid, false);
            if($arr) {
                array_unshift($arr,$organizationid);
            } else {
                $arr[] = $organizationid;
            }
            return $arr;
        } else {
            $arr = getClassify_index($organizations, 'id', 'pid', $organizationid);
            foreach ($organizations as $val) {
                if($organizationid == $val['id']) {
                    $re_arr = $val;
                    break;
                }
            }
            $re_arr[config('classify_name')] = $arr;
            return $re_arr;
        }
    }

    /**获取当前机构子机构id
     * @param $id当前机构id
     * @return array子机构id数组
     */
    public function getChildrenids($id) {
        $organization = new Organization;
        $res = $organization->where('status',0)->select();
        return $this->getChildrenid($res, $id);
    }

    /**获取当前机构子机构id
     * @param $res 所有机构
     * @param $id 当前机构id
     * @return array 子机构id数组
     */
    public function  getChildrenid($res, $id) {
        static $arr = array();
        foreach ($res as $k=>$v) {
            if ( $id == $v['pid'] ) {
                $arr[] = $v['id'];
                $this->getChildrenid($res, $v['id']);
            }
        }
        return $arr;
    }
    /*
     * 得到某个机构的单个字段的值
     * @param array $where 查询限制条件
     * @param string $str 查询的字段
     */
    public function getInfo($where, $str) {
        $info = $this->where('status', 0)->where($where)->value($str);
        if (!$info) {
            echo getBack(1, '获取机构信息失败！');
            exit;
        } else {
            return $info;
        }
    }
    /*
     * 根据两个机构id获取两个机构中间的所有机构信息，包括两个机构自身
     * @param string $head_id 第一个机构id
     * @param string $end_id 最后一个机构id
     * @param string $field 需要得到的字段信息，默认为所有
     */
    public function getProtionOrg($head_id, $end_id , $filed = '*') {
        $parentstr = $this->where(['status' => 0, 'id' => $end_id])->value('parentstr');
        $str = strstr($parentstr, $head_id) . ',' . $end_id;
        $info = collection($this->where('status', 0)->where('id', 'in', $str)->field($filed)->select())->toArray();
        return $info;
    }

    /**获取下一级机构
     * @author 叶文
     * @param $id 当前机构
     * @return array 下一级机构数组
     */
    public function nextOrg($id) {
        $org = new Organization;
        $res = $org->where('status',0)->select();
        $arr = array();
        foreach ($res as $k=>$v) {
            if($id == $v['pid']) {
                $arr[] = $v['id'];
            }
        }
        return $arr;
    }
}