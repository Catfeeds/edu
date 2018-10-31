<?php
namespace app\service\controller;
use app\service\model\ClassroomUser;

class ClassroomUserCon extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /*
     * 课程添加实现
     * @param 类型 参数名 参数说明
     * pid课程父id ,department系id,category分类id,fullname全名,shortname短名,idnumber课程编号,summary概要,coursepic课程图片,startdate开始时间,type属性
     * status状态,numsections周期
     */
    public function addClassSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $course = new ClassroomUser();

    }
    /*
     * 课程编辑实现
     * @param 类型 参数名 参数说明
     */
    public function upClassSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $course = new ClassroomUser();
    }
    /*
     * 课程查询实现
     * @param 类型 参数名 参数说明
     */
    public function queClassSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //通过$data['que_type']的值判断查询方式  直接传数据表字段
        $course = new ClassroomUser();

    }
    /*
     * 课程删除实现
     * @param 类型 参数名 参数说明
     */
    public function delClassSections() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        //通过$data['del_type']的值和用户角色判断删除方式  一般的修改status值，最高权限直接从数据表删除
        $course = new ClassroomUser();

    }
    /**@author  叶文
     * 通过学号查询人员信息
     */
    public function userInfo() {
        $data = $this->data;
        $courseuser = new ClassroomUser();
        $idnumber = $data['idnumber'];
        $res = $courseuser->userInfo($idnumber);
        if($res) {
            return getBack(1, $res);
        }else {
            return getBack(0, '用户不存在');
        }
    }
    /**@author  叶文
     * 通过用户id 获取信息
     */
    public function idUserInfo() {
        $data = $this->data;
        $courseuser = new ClassroomUser();
        $userid = $data['userid'];
        $res = $courseuser->id_userInfo($userid);
        if($res) {
            return getBack(1, $res);
        }else {
            return getBack(0, '用户不存在');
        }
    }
    /**@author  叶文
     * 通过课堂id获取课堂下人员信息
     */
    public function classmateInfo(){
        $data = $this->data;
        $courseuser = model('ClassroomUser');
        if (@empty($data['id'])) {
            return getBack(1, '参数错误！');
        }
        if (@empty($data['page'])) {
            $configPage[config('paginate.var_page')] = 1;
        } else {
            $configPage[config('paginate.var_page')] = $data['page'] < 1 ? 1 : $data['page'];
        }
        if (@empty($data['rows'])) {
            $configPage['list_rows'] = config('paginate.list_rows');
        } else {
            $configPage['list_rows'] = $data['rows'] < 0 ? config('paginate.list_rows') : $data['rows'];
        }
        $search = false;
        if (@!empty($data['search'])) {
            $search = '%' . $data['search'] . '%';
        }
        $res = $courseuser->ClassmateInfo($data['id'], $configPage, $search);
        if (!$res) {
            return getBack(1, '暂无人员信息！');
        }
        $res = $this->dataPageDispose($res);
        $ids = [];
        array_walk($res['data'], function(&$val, $key) use (&$ids){
            if ($val['role'] == 1) {
                $val['role'] = '学生';
            } else {
                $val['role'] = '老师';
            }
            $val['profession'] = '';
            $ids[] = $val['id'];
        });
        $professionUser = model('ProfessionUser');
        $join = [
            ['__PROFESSION__ p', 'p.id = pu.profession']
        ];
        $where = ['pu.user' => ['in', $ids], 'p.status' => 0];
        $field = 'p.name, pu.user, pu.sign';
        $ids = $professionUser->alias('pu')->where($where)->join($join)->field($field)->select();
        if (!$ids) {
            return getBack(0, $res);
        }
        foreach ($ids as $value) {
            foreach ($res['data'] as $k => $v) {
                if ($v['id'] == $value->user) {
                    $res['data'][$k]['profession'] = $value->name;
                    break;
                }
            }
        }
        return getBack(0, $res);
    }
    /*
     * 课堂添加成员
    */
    public function addUser() {
        $data = $this->data;
        if ( @empty($data['classroom']) || @empty($data['type']) || @empty($data['id']) || @empty($data['role'])) {
            return getBack(1, '参数错误！');
        }
        $classroom = model('Classroom');
        $classroomInfo = $classroom->where(['status' => 0, 'id' => $data['classroom']])->field('category')->find();
        if (!$classroomInfo) {
            return getBack(1, '该课堂不存在！');
        }
        switch ($data['type']) {
            case '1'://通过班级添加
                $class = model('Classes');
                $classInfo = $class->where(['id' => $data['id'], 'status' => 0])->find();
                if (!$classInfo) {
                    return getBack(1, '该班级不存在！');
                }
                $user = model('User');
                $users = $user->where(['class' => $data['id'], 'status' => 0])->field('id')->select();
                if (!$users) {
                    return getBack(1, '该班级下，暂无用户！');
                }
                $list = [];
                foreach ($users as $value) {
                    $list[] = [
                        'classroom_id' => $data['id'],
                        'user_id' => $value->id,
                        'type' => $classroomInfo->category,
                        'elective_way' => 2,
                        'role' => $data['role'] == 1 ? 1 : 0
                    ];
                }
                $classroomUser = model('ClassroomUser');
                $res = $classroomUser->allowField(true)->saveAll($list);
                break;
            case '2'://用户id添加
                $user = model('User');
                if (!is_array($data['id'])) {
                    return getBack(1, '参数错误！');
                }
                $users = $user->where('id', 'in', $data['id'])->field('id, realname, status')->select();
                if (!$users) {
                    return getBack(1, '选取用户不存在！');
                }
                $list = [];
                $msg = false;
                foreach ($users as $v) {
                    if ($v->status === 0) {
                        $list[] = [
                            'classroom_id' => $data['classroom'],
                            'user_id' => $v->id,
                            'type' => $classroomInfo->category,
                            'elective_way' => 1,
                            'role' => $data['role'] == 1 ? 1 : 0
                        ];
                    } else {
                        $msg .= $v->realname . '不存在' . ',';
                    }
                }

/*                foreach ($data['id'] as $value) {
                    foreach ($users as $v) {
                        if ($value == $v->id && $v->status === 0) {
                            $list[] = [
                                'classroom_id' => $value,
                                'user_id' => $value,
                                'type' => $classroomInfo->category,
                                'elective_way' => 1,
                                'role' => $data['role'] == 1 ? 1 : 0
                            ];
                        } else {
                            $msg .= $v->realname . '不存在' . ',';
                        }
                    }
                }*/
                if ($msg) {
                    $msg = substr_replace($msg, '。', -1, 1);
                    return getBack(1, $msg);
                }
                if (empty($list)) {
                    return getBack(1, '请重新选择用户！');
                }
                $classroomUser = model('ClassroomUser');
                $res = $classroomUser->allowField(true)->saveAll($list);
                break;
            default:
                return getBack(1, '参数错误！');
                break;
        }
        if (!$res) {
            return getBack(1, '添加失败！');
        } else {
            return getBack(0, '添加成功！');
        }
    }

    /*
     * 删除课堂下的用户
    */
    public function delUser(){
        $data = $this->data;
        if (@!empty($data['classroom']) || @!empty($data['type']) || @!empty($data['id']) ) {
            return getBack(1, '参数错误！');
        }
        $classroom = model('Classroom');
        $classroomInfo = $classroom->where(['status' => 0, 'id' => $data['classroom']])->find();
        if (!$classroomInfo) {
            return getBack(1, '该课堂不存在！');
        }
        switch ($data['type']) {
            case '1':
                $class = model('Classes');
                $classInfo = $class->where(['id' => $data['id'], 'status' => 0])->find();
                if (!$classInfo) {
                    return getBack(1, '该班级不存在！');
                }
                $user = model('User');
                $users = $user->where(['class' => $data['id'], 'status' => 0])->field('id')->select();
                if (!$users) {
                    return getBack(1, '该班级下，暂无用户！');
                }
                $classroomUser = model('ClassroomUser');
                foreach ($users as $value) {
                    $list = ['classroom_id' => $data['classroom'], 'user_id' => $value->id];
                    $classroomUser->where($list)->delete();
                }
                return getBack(0, '删除成功！');
                break;
            case '2':
                if (!is_array($data['id'])) {
                    return getBack(1, '参数错误！');
                }
                $classroomUser = model('ClassroomUser');
                foreach ($$data['id'] as $v) {
                    $list = ['classroom_id' => $data['classroom'], 'user_id' => $v];
                    $classroomUser->where($list)->delete();
                }
                return getBack(0, '删除成功！');
                break;
            default:
                return getBack(1, '参数错误！');
                break;
        }
    }

}