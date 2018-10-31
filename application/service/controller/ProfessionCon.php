<?php
namespace app\service\controller;

use app\service\model\Profession;

class ProfessionCon extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /**
     * 查询专业列表  
     * 模糊查询：根据专业名查询
     * 精确查询：根据机构id查询
     * 精确查询：用户id查询
     * @author 宋玉琪
     */
    public function index()
    {
        $data = $this->data;
        $profession = model('Profession');
        $where = array();
        if(isset($data['organization']))
        {
            $where['organization'] = ['=', $data['organization']];
        }
        if(isset($data['name']))
        {
            $where['name'] = ['like','%'.$data['name'].'%'];
        }
        else if(!isset($data['organization']))
        {
            $user = model('User');
            $orgpid = $user->where('id',$data['uid'])->value('pid');
            if(is_null($orgpid))
            {
                return getBack(1,"没有查找到当前用户的所在机构");
            }
            $where['organization'] = ['=',$orgpid];
        }
        $where['status'] = 0;
        $list = $profession->searchByWhere($where);
        $info = array();
        if($list)
        {
            foreach($list as $item)
            {
                $arr = array();
                $arr['id'] = $item['id'];
                $arr['name'] = $item['name'];
                $arr['code'] = $item['code'];
                array_push($info,$arr);
            }
            return getBack(0,$info);
        }
        return getBack(0,"没有查询到结果");
    }
    /*
     * 专业添加实现
     * @param 类型 参数名 参数说明
     */
    public function addProfession() {
        $data = $this->data;
        $profession = new Profession();
        $add = $profession->addProfession($data);
        return $add;
    }
    /*
     * 专业查询实现
     * @param 类型 参数名 参数说明
     */
    public function queProfession() {
        $data = $this->data;
        $profession = new Profession();
        $que = $profession->queProfession($data);
        return $que;
    }
    /*
     * 专业编辑实现
     * @param 类型 参数名 参数说明
     */
    public function upProfession() {
        $data = $this->data;
        $profession = new Profession();
        $up = $profession->upProfession($data);
        return $up;
    }
    /*
     * 专业删除实现
     * @param 类型 参数名 参数说明
     */
    public function delProfession() {
        $data = $this->data;
        $profession = new Profession();
        if (!isset($data['ids'])) {
            return getBack(1, '参数错误！');
        }
        $del = $profession->delManyProfession($data['ids']);
        if ($del) {
            return getBack(0, '删除成功！');
        } else {
            return getBack(1, '删除失败！');
        }
    }
    /*
     * 得到某一机构下的所有专业以及该机构下的所有机构信息
     */
    public function getOrgPro() {
        $profession = new Profession();
        $data = $profession->getOrgAndPro($this->data);
        return getBack(0, $data);
    }
    /*
     * 根据专业id得到专业信息
     */
    public function getProInfo() {
        $profession = new Profession();
        $data = $profession->getProInfo($this->data);
        return getBack(0, $data);
    }
    /*
     * 根据机构id得到该机构下的所有有效专业
     */
    public function getProFOrg() {
        $data = $this->data;
        $pro = new Profession();
        $result = $pro->quePort($data, 'getInfoForOrg');
        return getBack(0, $result);
    }
    /*
     * 根据专业id及其它条件查询某专业下的用户,实现分页
     */
    public function getProUser() {
        $data = $this->data;
        $Profession = model('Profession');
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
        $page = 1;
        if (isset($data['page']) && (int)$data['page'] > 0) {
            $page = $data['page'];
        }
        $rows = config('paginate.list_rows');
        if (isset($data['rows']) && (int)$data['rows'] > 0) {
            $rows = $data['rows'];
        }
        $result = $Profession->getProUserList($where, $page, $rows, $data);
        if ($result) {
            $result = $this->dataPageDispose($result);
            return getBack(0, $result);
        } else {
            return getBack(1, '查询失败!');
        }
    }
    /*
     * 查询专业下成员信息1
     */
    public function queProUser() {
        $data = $this->data;
        $pro = new Profession();
        $result = $pro->queProfessionPerson($data);
        return getBack(0, $result);
    }
    /*
     * 根据专业id获取该专业之外的所有学生，实现分页
     */
    public function getProOtherUser() {
        $data = $this->data;
        $page = 1;
        if (isset($data['page']) && (int)$data['page'] > 0) {
            $page = $data['page'];
        }
        $rows = config('paginate.list_rows');
        if (isset($data['rows']) && (int)$data['rows'] > 0) {
            $rows = $data['rows'];
        }
        $ProfessionUser = model('ProfessionUser');

        $result = $ProfessionUser->getOtherUser($data['id'], $page, $rows);
        if (!$result) {
            return getBack(1, '无可用信息！');
        }
        $result = $this->dataPageDispose($result);
        return getBack(0, $result);
    }
    /*
     * 显示用户信息（学生）
     */
    public function queStuUser() {
        $data = $this->data;
        $pro = new Profession();
        $result = $pro->queStuUser($data);
        return getBack(0, $result);
    }
    /*
     * 模糊显示用户信息（学生）
     */
    public function queStuUserForLike() {
        $data = $this->data;
        $pro = new Profession();
        $result = $pro->queStuUser_Like($where=[],$data);
        return getBack(0, $result);
    }
    /*
     * 删除专业与成员关系
     */
    public function delProUser() {
        $data = $this->data;
        $ProfessionUser = model('ProfessionUser');
        $where = [];
        if (!isset($data['id']) || !isset($data['user_id'])) {
            return getBack(1, '参数错误！');
        }
        $where['profession'] = $data['id'];
        $where['user'] = ['in', $data['user_id']];
        $result = $ProfessionUser->delWhere($where);
        if ($result) {
            return getBack(0, '操作成功！');
        }
        return getBack(1, '操作失败！');
    }
    /*
     * 批量添加专业下的成员
     */
    public function addBatchProUser() {
        $data = $this->data;
        $ProfessionUser = model('ProfessionUser');
        $result = $ProfessionUser->addManyProfessionPerson($data);
        if ($result) {
            return getBack(0, '操作成功！');
        }
        return getBack(1, '操作失败！');
    }

}