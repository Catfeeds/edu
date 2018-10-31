<?php
namespace app\service\controller;

use app\service\model\Classes;
use app\service\model\User;

class ClassCon extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /*
     * 班级添加实现
     * @param 类型 参数名 参数说明
     */
    public function addClass() {
        $data = $this->data;
        $classes = model('Classes');
        if ($classes->addClass($data)) {
            return getBack(0, '操作成功！');
        } else {
            return getBack(1, '操作失败！');
        }
    }
    /*
     * 通过班级id查询班级
     * @param 类型 参数名 参数说明
     */
    public function aqueClass() {
        $data = $this->data;
        $classes = new Classes;
        return $classes->aqueClass($data);
    }
    /**通过机构id查询班级
     * @return string
     */
    public function getClassForOrg() {
        $data = $this->data;
        $classes = new Classes;
        $result = $classes->queClassForOrg($data);
        if (!$result) {
            return getBack(1, '暂无班级！');
        }
        return getBack(0, $result);
    }
    /**
     * 获取当前用户所在机构以及子机构所有班级
     */
    public function cqueClass() {
        $data = $this->data;
        $classes = new Classes;
        $id=$data['uid'];
        return $classes->cqueClass($id);
    }
    /*根据机构id，获取该机构下的子机构的所有班级信息和子机构信息
     * @return string
     */
    public function classesOrg() {
        $data = $this->data;
        $classes = new Classes;
        $id = $data['id'];
        $uid = $data['uid'];
        return $classes->classesOrg($id,$uid);
    }
    /*
     * 班级编辑实现
     * @param 类型 参数名 参数说明
     */
    public function upClass() {
        $data = $this->data;
        $classes = model('Classes');
        $result = $classes->upClass($data);
        if ($result) {
            return getBack(0, '操作成功！');
        } else {
            return getBack(1, '操作失败！');
        }
    }
    /*
     * 班级删除实现
     * @param 类型 参数名 参数说明
     */
    public function delClass() {
        $data = $this->data;
        $classes = new Classes;
        $ids = $data['id'];
        return $classes->delClass($ids);
    }
    /*
     * 查询指定班级下所有人员
     * @param id 班级id
     */
    public function queClassmate() {
        $data = $this->data;
        if (isset($data['id'])) {
            $where = ['c.status' => 0, 'user.status' => 0, 'user.id' => $data['id']];
        } else {
            return getBack(1, '参数错误！');
        }
        $page = 1;
        if (isset($data['page'])) {
            $data['page'] > 0 ? $page = $data['page'] : $page = 1;
        }
        $row = config('paginate.list_rows');
        if (isset($data['rows'])) {
            $data['rows'] > 0 ? $row = $data['rows'] : '';
        }
        if (isset($data['number'])) {
            $where['user.number'] = $data['number'];
        }
        if (isset($data['username'])) {
            $where['user.username'] = ['like', '%' . $data['username'] . '%'];
        }
        if (isset($data['name'])) {
            $where['user.realname'] = ['like', '%' . $data['name'] . '%'];
        }
        $classes = new Classes;
        $result = $classes->queClassmate($where, $row, $page);
        if ($result) {
            return getBack(0, $this->dataPageDispose($result));
        } else {
            return getBack(0, '');
        }
    }
    /*
     * 获取没有班级的所有学生
    */
    public function getOtherClassUser() {
        $data = $this->data;
        $page = 1;
        isset($data['page']) && (int)$data['page'] > 0 && $page = $data['page'];
        $rows = config('paginate.list_rows');
        isset($data['rows']) && (int)$data['rows'] > 0 && $rows = $data['rows'];

        $user = new User;
        $where['user.status'] = 0;
        if (@!empty($data['id'])) {
            $where['user.class'] = ['neq', $data['id']];
        } else {
            $where['user.class'] = 'null';
        }
        $null = ['class' => 'null'];
        $info = $user->getUserWhere($where, $page, $rows, 'user.id, username, realname, idnumber', $null);
        if (!$info) {
            return getBack(1, '暂无数据！');
        }
        $info = $this->dataPageDispose($info);
        return getBack(0, $info);
    }
    /**给指定班级添加用户
     * @param id 用户id数组 class 班级id
     */
    public function addClassmate() {
        $data = $this->data;
        if (!isset($data['id']) || !isset($data['class']) || !is_array($data['id'])) {
            return getBack(1, '参数错误！');
        }
        $user = model('User');
        $list = [];
        foreach ($data['id'] as $v) {
            $list[] = ['id' => $v, 'class' => $data['class']];
        }
        if ($user->allowField(true)->saveAll($list)) {
            return getBack(0, '操作成功！');
        }
        return getBack(1, '操作失败！');
    }
    /*
     * 删除某班级下某个成员
     * @param userid 用户id 必须以数组形式
     */
    public function delClassmate() {
        $data = $this->data;
        $ids = $data['ids'];
        $class = $data['class'];
        $classes = new Classes;
        return $classes->delClassmate($ids,$class);
    }
    /**
     * 查询班级列表  
     * 模糊查询：根据班级名查询
     * 精确查询：根据机构id查询
     * 精确查询：用户id查询
     * @author 宋玉琪
     */
    public function getClass()
    {
        $data = $this->data;
        $class = model('Classes');
        $where = array();
        if(isset($data['organization']))
        {
            $where['department'] = ['=', $data['organization']];
        } else {
            $user = model('User');
            $orgpid = $user->where(['id' => $data['uid'], 'status' => 0])->value('pid');
            if($orgpid)
            {
                return getBack(1,"没有查找到当前用户的所在机构");
            }
            $where['department'] = ['=', $orgpid];
        }
        if(isset($data['name']))
        {
            $where['name'] = ['like', '%' . $data['name'] . '%'];
        }
        $where['status'] = 0;
        $list = $class->searchByWhere($where, 'id, name, describe, code');
        $info = array();
        if($list)
        {
            return getBack(0, $list);
        }
        return getBack(0, "没有查询到结果");
    }
}