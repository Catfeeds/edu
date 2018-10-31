<?php
namespace app\service\model;

use think\Model;
use app\admin\model\RoleUser;
use app\admin\model\Role;
use app\service\validate\Validates;
use think\Session;

class User extends Model
{

    protected function initialize()
    {
        parent::initialize();
    }

    protected $pk = 'id';

    // 新增id自动完成
    protected $auto = [
        'id'
    ];

    protected function setIdAttr($val)
    {
        return $val ? $val : createGuid();
    }

    protected function getPrevtimeAttr($value) {
        return date("Y-m-d H:i:s", $value);
    }

    // 条件更新
    public function userUpdate($where, $data)
    {
        return $this->where($where)->update($data);
    }

    /*
     * 登录
     */
    public function login()
    {
        /*
         * 字符串写法
         * $where2 = 'phone = :phone or username = :username';
         * $bind = ['phone' => $this->data['username'], 'username' => $this->data['username']];
         * $userInfo = $this->where($where2)->bind($bind)->find();
         */
        // 学号或手机号码登录
        $userInfo = $this->where([
            'status' => 0,
            'phone|username' => $this->data['username']
        ])->find();
        if (! $userInfo) {
            return getBack(1, '当前账户不存在，请重新登录！'); // 0：成功,1:失败
        }
        if ($userInfo['password'] === md5(md5($this->data['userpwd']) . $userInfo['salt'])) {
            $Rbac = new \org\util\Rbac($userInfo['id']);
            $access_list = $Rbac::saveAccessList();
            $saveData = [
                'logintime' => time(),
                'ip' => $this->data['request_ip']
            ];
            $where2['id'] = $userInfo['id'];
            $number = $this->userUpdate($where2, $saveData);
            // $number = $this->where($where2)->update($saveData);
            $keyArr = config('user_list');
            foreach ($keyArr as $v) {
                if ($v == 'access_list') {
                    $reData[$v] = $access_list;
                } else if ($v == 'uid') {
                    $reData[$v] = $userInfo['id'];
                } else {
                    $reData[$v] = $userInfo[$v];
                }
            }
            $roleUser = new RoleUser();
            $org = new Organization();
            $reData['org_name'] = $org->getInfo([
                'id' => $userInfo['pid']
            ], 'name');
            $roleUserInfo = $roleUser->getUserRoleInfo($userInfo['id']);
            $reData['role'] = $roleUserInfo['remark'];
            $reData['logintime'] = date('Y-m-d H:i:s', $reData['logintime']);
            if ($number > 0) {
                return getBack(0, $reData, 'createSession'); // 0：成功,1:失败
            } else {
                /*
                 * Session::clear();
                 * Session::clear('think');
                 * Session::flush();
                 */
                return getBack(1, '内部错误，请联系相关人员！'); // 0：成功,1:失败
            }
        } else {
            return getBack(1, '密码错误！'); // 0：成功,1:失败
        }
    }

    /**
     *
     * @author 李桐
     *         生成随机数
     * @param int $num
     *            得到几位数的随机数
     */
    public function createRandomNumber($num = 6)
    {
        $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randnum = '';
        for ($i = 0; $i < $num; $i ++) {
            $randnum .= $str{mt_rand(0, 35)};
        }
        $re_num = md5($randnum);
        return substr($re_num, 0, $num);
    }

    /*
     * 根据用户输入组合密码
     * @param string $pass 输入密码
     * return array ['password' => '加密密码', 'random' => '加盐']
     */
    public function assemblypass($pass)
    {
        $random_num = $this->createRandomNumber();
        return [
            'password' => md5(md5($pass) . $random_num),
            'random' => $random_num
        ];
    }

    /**
     *
     * @author 李桐
     *         新增用户
     * @param
     *            参数类型 参数名 参数用途
     * @param
     *            varchar(100) username 新增的信息
     * @param
     *            varchar(50) realname 新增的信息
     * @param
     *            varchar(255) idnumber 新增的信息
     * @param
     *            varchar(100) email 新增的信息
     * @param
     *            varchar(20) phone 新增的信息
     * @param
     *            char(32) pid 新增的信息
     * @param
     *            varchar(70) adress 新增的信息
     * @param
     *            varchar(45) request_ip 新增的信息
     * @param
     *            longtext description新增的信息
     * @param
     *            char(32) class 新增的信息
     * @param
     *            char(20) eduction 新增的信息
     * @param
     *            int(11) enter_time 新增的信息
     * @param
     *            varchar(32) password 新增的信息
     * @param
     *            char(32) profession 新增的信息
     *            说明:如果用户为老师时则不用传class、profession这两个字段
     */
    public function addUser($data)
    {
        if (!$this->validate('User.add')->data($data)) {
            echo getBack(1, $this->getError());
            exit;
        }
        // 同一机构下编码唯一或整个系统电话号码唯一
        $result = $this->where(['pid' => $data['org_id'], 'status' => 0, 'idnumber' => $data['idnumber']])->find();
        if ($result) {
            echo getBack(1, '用户编码已存在！');
            exit();
        }
        // 生成用户密码
        $sys_pass = $this->assemblypass($data['password']);
        $data['password'] = $sys_pass['password'];
        $data['salt'] = $sys_pass['random'];
        $data['timecreated'] = time();
        $data['status'] = 0;
        $data['pid'] = $data['org_id'];
        // 用户头像
        if (isset($data['upload_files'])) {
            $headportrait = end($data['upload_files']);
            $atta = new Attachments();
            $data['picture'] = $atta->saveFile($headportrait['hash'], $headportrait['file']);
            unset($data['upload_files']);
            unset($data['upload_files_hashs']);
        }
       // 添加用户
        $result = $this->allowField(true)->data($data)->save();
        $id = $this->id;
        return $this->id;
    }

    /**
     *
     * @author 李桐
     *         删除用户
     * @param
     *            参数类型 参数名 参数用途
     * @param
     *            array id 定位需要删除信息的位置
     */
    public function delUser($data)
    {
        $num = count($data['id']);
        for ($i = 0; $i < $num; $i ++) {
            $list[$i] = ([
                'id' => $data['id'][$i],
                'status' => '1',
                'timemodified' => time()
            ]);
        }
        $res = $this->saveAll($list);
        if ($res) {
            return getBack(0, 1);
        } else {
            return getBack(1, "删除失败！");
        }
    }

    /**
     *
     * @author 李桐
     *         修改用户
     * @param
     *            参数类型 参数名 参数用途
     * @param
     *            varchar(100) username 需要修改的信息
     * @param
     *            varchar(100) email 需要修改的信息
     * @param
     *            varchar(20) phone 需要修改的信息
     * @param
     *            char(32) pid 需要修改的信息
     * @param
     *            varchar(70) adress 需要修改的信息
     * @param
     *            char(255) picture 需要修改的信息
     * @param
     *            longtext description需要修改的信息
     * @param
     *            char(32) class 需要修改的信息
     * @param
     *            char(20) eduction 需要修改的信息
     * @param
     *            int(11) enter_time 需要修改的信息
     * @param
     *            varchar(32) password 需要修改的信息
     * @param
     *            char(32) profession 需要修改的信息
     *            说明：1、如果用户为老师时则不用传class、profession这两个字段
     *            2、如果用户只修改了其中几项，则没修改的安装原内容传给后端，以上字段除了class、profession这两个字段可以为空，其余字段不能为空
     */
    public function upUser($data)
    {
        if (!$this->validate('User.edit')->data($data)) {
            echo getBack(1, $this->getError());
            exit;
        }
        if (isset($data['password'])) {
            $sys_pass = $this->assemblypass($data['password']);
            $data['password'] = $sys_pass['password'];
            $data['salt'] = $sys_pass['random'];
        }
        $this->timemodified = time();
        $res = $this->allowField(true)->save($data, [
            'id' => $data['id']
        ]);
        if ($res) {
            return getBack(0, '编辑成功！');
        } else {
            return getBack(1, "修改失败！");
        }
    }

    /**
     *
     * @author 李桐
     *         通过id查询用户
     * @param
     *            参数类型 参数名 参数用途
     * @param
     *            char(32) id 查询信息
     * @param
     *            char(32) uid 当前登录用户id
     */
    public function queIdUser($data)
    {
        $id[0] = $data['uid'];
        $id[1] = $data['id'];
        $temp = $this->where('id', 'in', $id)
            ->where('status', '0')
            ->select();
        $res = $this->queMessage($data['id'], $data['uid'], $temp);
        if ($res) {
            return getBack(0, $res);
        } else {
            return getBack(1, "查询失败！");
        }
    }

    /*
     * 通过id获取用户详情
     * @param array $data ['uid' => '登陆用户id', 'id' => '查询用户id']
     */
    public function getUserInfoId($data)
    {
        if (empty($data['id'])) {
            echo getBack(1, '请求错误！');
            exit();
        }
        // 获取用户详情-包括班级
        $user_info = $this->where([
            'status' => 0,
            'id' => $data['id']
        ])
            ->find()
            ->toArray();
        if (! $user_info) {
            echo getBack(1, '暂无该用户信息！');
            exit();
        }
        if ($user_info['class']) {
            $Class = new Classes();
            $class_name = $Class->where([
                'status' => 0,
                'id' => $user_info['class']
            ])->value('name');
            $user_info['class_name'] = $class_name;
        } else {
            $user_info['class_name'] = '';
        }
        $user_info['enter_time'] = date('Y年m月d日', $user_info['enter_time']);
        // 获取用户专业信息
        $pro = new Profession();
        $join = [
            [
                '__PROFESSION_USER__ prouser',
                'prouser.profession = pro.id'
            ],
            [
                '__ORGANIZATION__ org',
                'org.id = pro.organization'
            ]
        ];
        $pro_info = collection($pro->alias('pro')
            ->where([
            'pro.status' => 0,
            'prouser.user' => $data['id'],
            'org.status' => 0
        ])
            ->join($join)
            ->order('prouser.sign')
            ->field('prouser.sign, pro.id, pro.name')
            ->select())->toArray();
        $user_info['pro'] = $pro_info;
        // 机构信息
        $org = new Organization();
        $uid_org_id = $this->getOrgInfo($data['uid']);
        $org_info = $org->getProtionOrg($uid_org_id, $user_info['pid'], 'name');
        $user_info['org_name'] = $org_info;
        return $user_info;
    }

    /**
     *
     * @author 李桐
     *         通过用户名模糊查询用户
     * @param
     *            参数类型 参数名 参数用途
     * @param
     *            varchar(100) username 查询信息
     * @param
     *            char(32) uid 当前登录用户id
     */
    public function queUserName($data)
    {
        $username = $data['username'];
        $temp1[0] = $this->where('id', $data['uid'])
            ->where('status', '0')
            ->find();
        $temp2 = $this->where('username', 'like', "%$username%")
            ->where('status', '0')
            ->select();
        $num = count($temp2);
        for ($i = 0; $i < $num; $i ++) {
            $temp1[1] = $temp2[$i];
            $result[$i] = $this->queMessage($temp2[$i]['id'], $data['uid'], $temp1);
        }
        if ($result) {
            return getBack(0, $result);
        } else {
            return getBack(1, "查询失败！");
        }
    }

    /**
     *
     * @author 李桐
     *         通过学号查询用户
     * @param
     *            参数类型 参数名 参数用途
     * @param
     *            varchar(255) idnumber 查询信息
     * @param
     *            char(32) uid 当前登录用户id
     */
    public function queIdNumber($data)
    {
        $temp1 = $this->where('id', $data['uid'])
            ->where('status', '0')
            ->find();
        $temp2 = $this->where('idnumber', $data['idnumber'])
            ->where('status', '0')
            ->find();
        $temp[0] = $temp1;
        $temp[1] = $temp2;
        $res = $this->queMessage($temp2['id'], $data['uid'], $temp);
        if ($res) {
            return getBack(0, $res);
        } else {
            return getBack(1, "查询失败！");
        }
    }

    /**
     *
     * @author 李桐
     *         通过班级查询用户
     * @param
     *            参数类型 参数名 参数用途
     * @param
     *            char(32) class 查询信息
     */
    public function queClass($data)
    {
        // $res = $this->where('class',$data['class'])->where('status','0')->select();
        $res = Db('User')->alias('user')
            ->join('Organization org', 'user.pid = org.id')
            ->where('class', $data['class'])
            ->where('user.status', 0)
            ->field('user.id, user.username, user.realname, user.idnumber, user.email,user.logintime, user.enter_time, org.name')
            ->paginate()
            ->toArray();
        return getBack(0, $res);
    }

    /**
     *
     * @author 李桐
     *         通过专业查询用户
     * @param
     *            参数类型 参数名 参数用途
     * @param
     *            char(32) profession 查询信息
     */
    public function queProfession($data)
    {
        $prouser = new ProfessionUser();
        $list = $prouser->que($data);
        $num = count($list);
        $i = 0;
        while ($i < $num) {
            $temp[$i]['id'] = $list[$i]['user'];
            $i = $i + 1;
        }
        $number = count($temp);
        $j = 1;
        $id = $temp[0]['id'];
        while ($j < $number) {
            $id = $id . "," . $temp[$j]['id'];
            $j = $j + 1;
        }
        $res = User::all($id);
        if ($res) {
            return getBack(0, $res);
        } else {
            return getBack(1, "查询失败！");
        }
    }

    /*
     * 根据用户id得到用户信息,默认获取机构id
     * @param string $id 用户id
     */
    public function getOrgInfo($id, $str = 'pid')
    {
        $where = [
            'user.status' => 0,
            'user.id' => $id,
            'org.status' => 0
        ];
        $info = $this->alias('user')
            ->where($where)
            ->join('__ORGANIZATION__ org', 'org.id = user.pid')
            ->value('user.' . $str);
        if (! $info) {
            echo getBack(1, '用户信息错误！');
            exit();
        } else {
            return $info;
        }
    }

    /**
     *
     * @author 李桐
     *         查询
     * @param
     *            参数类型 参数名
     * @param
     *            char(32) id 查询信息
     * @param
     *            Arrly res user表中的需要返回的数据
     * @param
     *            char(32) uid 当前登录用户的id
     */
    public function queMessage($id, $uid, $res)
    {
        $number = count($res);
        for ($i = 0; $i < $number; $i ++) {
            // 提取需要返回的相关信息
            if ($res[$i]['id'] == $uid) {
                $uidpid = $res[$i]['pid'];
            } else {
                $list['username'] = $res[$i]['username'];
                $list['realname'] = $res[$i]['realname'];
                $list['idnumber'] = $res[$i]['idnumber'];
                $list['email'] = $res[$i]['email'];
                $list['phone'] = $res[$i]['phone'];
                $list['address'] = $res[$i]['address'];
                $list['description'] = $res[$i]['description'];
                $list['education'] = $res[$i]['education'];
                $list['class'] = $res[$i]['class'];
                $list['enter_time'] = $res[$i]['enter_time'];
                $idpid = $res[$i]['pid'];
            }
        }
        // 返回机构信息
        $organization = new Organization();
        $list['organization'] = $organization->getProtionOrg($idpid, $uidpid);
        // 查询专业id
        $professionUser = new ProfessionUser();
        $temp = $professionUser->queId($id);
        $num = count($temp);
        $professionId = $temp[0]['profession'];
        for ($i = 1; $i < $num; $i ++) {
            $professionId = $professionId . "," . $temp[$i]['profession'];
        }
        // 通过专业id查询专业信息
        $profession = new Profession();
        $result = $profession->queAll($professionId);
        for ($i = 0; $i < $num; $i ++) {
            for ($j = 0; $j < $num; $j ++) {
                if ($result[$i]['id'] == $temp[$j]['profession']) {
                    $pro[$i]['professionName'] = $result[$i]['name'];
                    $pro[$i]['sign'] = $temp[$j]['sign'];
                }
            }
        }
        $list['profession'] = $pro;
        return $list;
    }

    /**
     * 根据条件获取用户信息 实现分页
     * 
     * @param null $page
     *            要查询某页
     * @param null $list_rows
     *            每页显示多少条
     * @param null $data
     *            其它数据
     */
    public function getUserWhere($where, $page, $rows, $field = '*', $data = false)
    {
        $configPage['list_rows'] = $rows > 0 ? $rows : config('paginate.list_rows');
        $configPage[config('paginate.var_page')] = $page > 0 ? $page : 1;
        if ($data && $data['class'] === 'null') {
            $join = [
                ['__ROLE_USER__ roleuser', 'roleuser.user_id = user.id '],
                ['__ROLE__ role', 'role.id = roleuser.role_id and role.name = "student"']
            ];
            $where['role.status'] = 1;
            $info = $this->alias('user')
                ->join($join)
                ->where($where)
                ->field($field)
                ->paginate($configPage, config('paginate.custom'))
                ->toArray();
        } else {
            $where['status'] = 0;
            $info = $this->where($where)
                ->field($field)
                ->paginate($configPage, config('paginate.custom'))
                ->toArray();
        }
        if (! $info) {
            return false;
        }
        return $info;
    }

    /*
     * 用户中心，用户列表，分页显示
     * @param array $data ['page' => '页数', 'rows' => '条数', 'uid' => '登陆用户id']
     */
    public function getUserList($data)
    {
        $org = new Organization();
        $role = new Role();
        $config_page[config('paginate.var_page')] = empty($data['page']) ? 1 : $data['page'] < 1 ? 1 : $data['page'];
        $config_page['list_rows'] = empty($data['rows']) ? 1 : $data['rows'] < 1 ? 1 : $data['rows'];
        // 根据用户id得到该用户及所有子机构id
        $user_org_id = $this->getOrgInfo($data['uid']);
        $org_alls = $org->getChildren($user_org_id, false);
        // 得到学生角色id
        $role_student = $role->where('name', 'student')->value('id');
        // 分页查询用户
        $where = [
            'user.status' => 0,
            'org.status' => 0,
            'roleuser.role_id' => $role_student
        ];
        $join = [
            [
                '__ORGANIZATION__ org',
                'org.id = user.pid'
            ],
            [
                '__ROLE_USER__ roleuser',
                'roleuser.user_id = user.id'
            ]
        ];
        $str = 'user.id, user.username, user.realname, org.name as org, user.email, user.idnumber, user.logintime as prevtime, user.enter_time as starttime';
        $user_alls = $this->alias('user')
            ->where($where)
            ->where('user.pid', 'in', $org_alls)
            ->join($join)
            ->field($str)
            ->order('user.timecreated')
            ->paginate($config_page, config('paginate.custom'))
            ->toArray();
        return getBack(0, $user_alls);
    }

    public function getUserList2($where, $page = false, $rows = false, $data = false)
    {
        $org = new Organization();
        $role = new Role();
        $config_page[config('paginate.var_page')] = $page ? $page : 1;
        $config_page['list_rows'] = $rows ? $rows : config('paginate.list_rows');
        if (!isset($where['user.pid'])) {
            $user_org_id = $this->getOrgInfo($data['uid']);
            $org_alls = $org->getChildren($user_org_id, false);
            $where['user.pid'] = ['in', $org_alls];
        }
        // 得到角色id
        if (!@empty($data['role'])) {
            $role_student = $role->where('name', $data['role'])->value('id');
            $where['roleuser.role_id'] = $role_student;
        }
        if (!isset($where['user.status'])) {
            $where['user.status'] = 0;
        }
        $where['org.status'] = 0;
        $join = [
            ['__ORGANIZATION__ org', 'org.id = user.pid' ],
            ['__ROLE_USER__ roleuser', 'roleuser.user_id = user.id']
        ];
        $str = 'user.id, user.username, user.realname, org.name as org, user.email, user.idnumber, user.logintime as prevtime, user.enter_time as starttime';
        $user_alls = $this->alias('user')
            ->where($where)
            ->join($join)
            ->field($str)
            ->order('user.timecreated')
            ->paginate($config_page, config('paginate.custom'))
            ->toArray();
        if ($user_alls) {
            return $user_alls;
        } else {
            return false;
        }
    }

    /*
     * 查询出所有已经分配和未分配班级和的用户
     * @author 李桐
     * @param 参数类型 参数名
     * @param int distribution （说明 distribution=0 表示查询未分配；distribution=1 表示查询已分配）
     */
    public function distribution($data)
    {
        if ($data['distribution']) {
            $result = $this->alias('user')
                ->join('Organization org', 'user.pid = org.id')
                ->where('user.status', 0)
                ->where('user.class', '<>', 'NULL')
                ->field('user.id, user.username, user.realname, user.idnumber, user.email,user.logintime, user.enter_time, org.name')
                ->paginate()
                ->toArray();
        } else {
            $result = $this->alias('user')
                ->join('Organization org', 'user.pid = org.id')
                ->where('user.status', 0)
                ->where('user.class', 'NULL')
                ->field('user.id, user.username, user.realname, user.idnumber, user.email,user.logintime, user.enter_time, org.name')
                ->paginate()
                ->toArray();
        }
        return getBack(0, $result);
    }

    /*
     * @author 李桐
     * 查询所有学生、老师、管理员
     * @param string member（member = student/teacher/admin）
     */
    public function queMember($data)
    {
        $result = Db('User')->alias('user')
            ->join('RoleUser role_user', 'user.id = role_user.user_id')
            ->join('Role role', 'role.id = role_user.role_id')
            ->join('Organization org', 'user.pid = org.id')
            ->where('role.name', $data['member'])
            ->where('user.status', 0)
            ->field('user.id, user.username, user.realname, user.idnumber, user.email,user.logintime, user.enter_time, org.name')
            ->paginate()
            ->toArray();
        return getBack(0, $result);
    }

    /*
     * @author 李桐
     * 查询机构下的所有成员
     * @param varchar(100) orgName
     * 根据前端传来的机构名称，查找这个机构的所有用户
     */
    public function queOrg($data)
    {
        $result = Db('User')->alias('user')
            ->join('Organization org', 'user.pid = org.id')
            ->where('org.name', $data['orgName'])
            ->where('user.status', 0)
            ->field('user.id, user.username, user.realname, user.idnumber, user.email,user.logintime, user.enter_time, org.name')
            ->paginate()
            ->toArray();
        return getBack(0, $result);
    }

    /**
     *
     * @author 贾芸吉
     *         用户批量新增
     *         根据前端传来的xlsx文件新增
     *         第一排字段为数据库字段名
     *         返回结果集
     *@param result['idnumber'] 失败未插入的学生id
     *@param result['Row']     失败未插入的学生所在行号
     *@param result['profession'] 新增profession表结果情况
     *@param result['RoleUser']  新增RoleUser表情况
     *   文件未删除
     */
    public function Insertlist($data) {
        vendor("PHPExcel.PHPExcel");

        $param = ['role' => '未选择角色', 'organization' => '未选择机构', 'class' => '未选择班级', 'profession' => '未选择专业'];
        foreach ($param as $key => $value) {
            if (@empty($data[$key])) {
                echo getBack(1, $value);
                exit;
            }
        }
        $file = $data['upload_files'][0]['file']; // 获取文件
        $err_msg = '';
        $msg = '';
        $path = ROOT_PATH . 'public' . DS . 'uploads';
        $info = $file->move($path);
        if ($info) {
            $file_name = $path . DS . $info->getSaveName(); // 上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = "utf-8"); // 加载文件内容,编码utf-8
            $excel_array = $obj_PHPExcel->getsheet(1)->toArray(); // 转换为数组格式
            $excel_title = $excel_array[0]; // 提取字段名
            array_shift($excel_array); // 删除第一个数组(字段);
        //    $objWorksheet = $obj_PHPExcel->getActiveSheet(1); // 获取工作表
        //    $highestRow = $objWorksheet->getHighestRow(); // 取得总行数
        //    $highestColumn = $objWorksheet->getHighestColumn(); // 取得总列数
            $success = 0;
            $error_arr = [];
            foreach ($excel_array as $v) {
                if (empty(current($v))) {
                    continue;
                }
                $data_save = array_combine($excel_title, $v);
                $sys_pass = $this->assemblypass($data_save['password']);
                $data_save['password'] = $sys_pass['password'];
                $data_save['salt'] = $sys_pass['random'];
                $data_save['timecreated'] = time();
                $data_save['pid'] = $data['organization'];
                $data_save['class'] = $data['class'];
                try{
                    $this->data($data_save,true)->isUpdate(false)->save();
                    $success++;
                    $roleuser_save[] = ['role_id' => $data['role'], 'user_id' => $this->id];
                    $prouser_save[] = ['profession' => $data['profession'], 'user' => $this->id, 'sign' => 1];
                }catch(\Exception $e){
                    $error_arr[] = $data_save['realname'];
                }
            }
            $msg = "总" . (count($error_arr) + $success) . "条信息，成功添加" . $success . "条，失败" . count($error_arr) . "条。";
            if ($name_str = implode(',', $error_arr)) {
                $err_msg .= '有' . $name_str . '未添加失败。</br>';
            }
            $role = new Role();
            if ($role->where(['id' => $data['role'], 'status' => 1])->find()) {
                $roleuser = new RoleUser();
                if (!$roleuser->saveAll($roleuser_save)) {
                    $err_msg .= '添加角色失败！</br>';
                }
            } else {
                $err_msg .= '添加角色失败，该角色不存在！</br>';
            }
            $profession = new Profession;
            if ($profession->where(['id' => $data['profession'], 'status' => 0])->find()) {
                $professionUser = new ProfessionUser();
                if (!$professionUser->saveAll($prouser_save)) {
                    $err_msg .= '添加专业失败！</br>';
                }
            } else {
                $err_msg .= '添加专业失败，该专业不存在！</br>';
            }
            if (is_file($file_name)) {
                @unlink($file_name);
            }
        } else {
            $msg = "总 0 条信息，成功添加 0 条，失败 0 条。";
            $err_msg = $file->getError();
        }
        return getBack(0, ['msg' => $msg, 'error' => $err_msg]);
    }
}
