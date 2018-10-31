<?php
namespace app\service\model;
use think\Model;
use app\admin\model\Role;
use app\admin\model\RoleUser;
use app\service\validate\Validates;

class Profession extends Model
{
    protected $pk = 'id';
    protected $auto = ['id'];

    protected function setIdAttr($val) {
        return $val ? $val : createGuid();
    }
    /**
     * 万能查询:根据WHERE查询
     * @author 宋玉琪
     * @param where 前端传入的查询条件
     */
    public function searchByWhere($where)
    {
        $res = $this->where($where)->select();
        return $res;
    }
    /**
     * 添加专业:根据机构表的相关字段完成添加
     * @author 陈博文
     * @param organization 前端传入的机构id
     * @param name         前端传入的专业名
     * @param status       新增专业status字段默认为0(0:正常，1:删除)
     */
    public function addProfession($data)
    {
        $vali = new Validates('pro');
        if (!$vali->check($data)) {
            return getBack(1, $vali->getError());
        }
        $res = $this->where('status',0)->where('organization',$data['organization'])->select();
        if(!$res){
            $data['status'] = 0;
            $data['timecreated'] = time();
            $list = $this->data($data)->allowField(true)->save();
            if (!$list) {
                return getBack(1, '添加错误！');
            } else {
                return getBack(0, $list);
            }
        }else{
            return $this->compare($res,$data);
        }
    }
    /**
     * 比较专业name和code是否存在
     * @author 陈博文
     * @param $res         机构下所有状态正常的专业
     * @param status       专业status字段默认为0(0:正常，1:删除)
     */
    public function compare($res,$data)
    {
        //有相同的专业名或编号
        for($i=0;$i<count($res);$i++){
                if($res[$i]['name']==$data['name'] || $res[$i]['code']==$data['code']){
                    return "exist";
                }
        }
        if(!isset($data['id'])) {
            $data['status'] = 0;
            $data['timecreated'] = time();
            $list = $this->data($data)->allowField(true)->save();
            if ($list){
                return $list;
            } else {
                return "添加错误！";
            }
        } else {
            $lick = $this->save([
                'name' => $data['name'],
                'code' => $data['code'],
                'describe' => $data['describe'],
                'timemodified' => time()
                ],['id' => $data['id']]);
            if($lick){
                return $lick;
            }else{
                return "添加错误！";
            }
        }
        
    }
    /**
     * 编辑专业:根据指定的专业id来完成专业的更新
     * @author 陈博文
     * @param id            前端传入的专业id
     * @param organization  前端传入的机构id
     */
    public function upProfession($data)
    {
        $status = $this->where('id',$data['id'])->value('status');
        $res = $this->where('status',0)->where('organization',$data['organization'])->select();
        if($status == 0){
            $info = $this->compare($res,$data);
            if($info == 1){
                return getBack(0,"更新成功！");
            }elseif ($info == "exist") {
                return getBack(1,"有相同的专业名或专业编号！");
            }
        }else{
            return getBack(1,"此专业已经删除，不能更新！");
        }
    }
    /**
     * 查询专业信息方式
     * @author 陈博文
     */
    public function queProfession($data)
    {
        if($data['type'] == 'id'){                //1.根据专业id获取该专业信息
            $idRes = $this->queIdMeg($data['id']);
            return getBack(0,$idRes);
        }
        if($data['type'] == 'part'){              //2.根据用户id获取该用户所能查看的专业信息
            $partRes = $this->quePartMeg($data['id']);
            return getBack(0,$partRes);
        }
        if($data['type'] == 'all'){
            $allRes = $this->getInfoForOrg($data['id']);    //3.根据机构id来获取所有的专业信息
            return getBack(0,$allRes);
        }
    }
    /**
     * 查看专业:根据专业id获取该专业的信息
     * @author 陈博文
     * @param id            前端传入的专业id
     */
    public function queIdMeg($id)
    {
        return $this->where(['id' => $id,'status' => 0])->select();
    }
    /**
     * 查看专业:获取用户所能查看的所有机构下的专业信息
     * @author 陈博文
     * @param id            前端传入的用户id
     * @param organization  前端传入的机构id
     */
    public function quePartMeg($id)
    {
        $organization = new Organization();
        $user = new User();
        //前端传入一个用户id在用户表中寻找此id对应的机构id(数据表中字段为pid)的值
        $organizationId = $user->where(['status' => 0,'id' => $id])->value('pid');       
        $User_OrganizationIds = $organization->getChildren($organizationId);//获取的当前用户所能查看的所有子机构
        $list = $this->where('status',0)->where('organization','in',$User_OrganizationIds)->select();
        if($list)
        {
            return getBack(0,$list);
        }else{
            return getBack(1,'此用户id下没有可以查看的机构信息！');
        }
    }
    /**
     * 根据机构id得到该机构下的所有有效专业信息
     * @author 陈博文
     * @param id    array      前端传入的机构id
     * @param $str  string     获取字段，
     */
    public function getInfoForOrg($id, $str = null)
    {
        if ($str === null) {
            $str = 'pro.*';
        } else {
            $str_arr = explode(',', $str);
            array_walk($str_arr, function(&$val, $key) {
                $val = 'pro.' . trim($val);
            });
            $str = implode(', ', $str_arr);
        }
        $pro_data = $this->alias('pro')->where(['pro.status' => 0, 'org.status' => 0, 'pro.organization' => $id])->join('__ORGANIZATION__ org', 'pro.organization = org.id')->field($str)->select();
        if ($pro_data) {
            return $pro_data;
        } else {
            return false;
        }
    }
    /**
     * 批量删除专业:通过更新status字段的值来完成数据的删除
     * @author 陈博文
     * @param ids    前端传入的批量专业id
     */
    public function delManyProfession($ids)
    {
        if (!is_array($ids)) {
            echo getBack(1, '参数错误');
            exit;
        }
        $proUser = new ProfessionUser();
        if($ids) {//前端传入ids的数组，批量删除格式:$ids=['id1','id2','id3'];
            $res = $this->where('id','in',$ids)->update(['status' => 1,'timemodified' => time()]);
            $proUser::where('profession','in',$ids)->delete();//将专业用户关系表中，所有涉及到该专业的数据信息，也删除
            if($res) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 查看专业成员:根据专业id，获取该专业下面的所有人员信息:用户id、用户名、用户编号(学号)
     * @author 陈博文 
     * @param id    专业id
     * @param row   每页显示条数
     * @param page  显示第几页
     */
    public function queProfessionPerson($data)
    {
        $user = new User();
        $proUser = new ProfessionUser();
        $configPage['list_rows'] = config('paginate.list_rows');
        isset($data['row']) && $data['row'] > 0 && $configPage['list_rows'] = $data['row'];
        $configPage[config('paginate.var_page')] = 1;
        isset($data['page']) && $data['page'] > 0 && $configPage[config('paginate.var_page')] = $data['page'];
        //查询某一个专业下的所有用户id：$UserID[]
        $UserId = $proUser->where('profession',$data['id'])->field('user')->select();      
        foreach ($UserId as $v) {
            $UserID[] = $v['user'];
        }
        //查询用户的：用户id、用户名、用户编号(学号)
        $ProMes = Db('user')
        ->alias('user')
        ->join('__PROFESSION_USER__ proUser','proUser.user = user.id')
        ->where('proUser.user','in',$UserID)
        ->field('user.id,user.username,user.idnumber')
        ->paginate(['list_rows' => $data['row'], 'page' => $data['page']])->toArray();
        return getBack(0,$ProMes);
    }
    /*
     * 根据条件查询某专业下面的人员信息，实现分页
      * $data array  ['id' => '专业id']
    */
    public function getProUserList($where, $page = false, $rows = false, $data) {
        $configPage['list_rows'] = $rows ? $rows : config('paginate.list_rows');
        $configPage[config('paginate.var_page')] = $page ? $page : 1;
        $User = new User();
        $ProfessionUser = new ProfessionUser();
        $pro_info = $this->where(['status' => 0, 'id' => $data['id']])->find();
        if (!$pro_info) {
            echo getBack(1, '当前专业不存在！');
            exit;
        }
        if (!isset($where['user.status'])) {
            $where['user.status'] = 0;
        }
        $where['proUser.profession'] = $data['id'];
        $join = [
            ['__USER__ user', 'user.id = proUser.user']
        ];
        $str = 'user.id as id, user.idnumber, user.realname, proUser.sign, user.username, user.email, user.enter_time, user.logintime';
        $info = $ProfessionUser->alias('proUser')->join($join)->where($where)->field($str)->order('user.timecreated')->paginate($configPage, config('paginate.custom'))->toArray();
        if ($info) {
            foreach ($info['data'] as $key => $value) {
                $info['data'][$key]['enter_time'] = date('Y-m-d', $value['enter_time']);
                $info['data'][$key]['logintime'] = date('Y-m-d H:i:s', $value['logintime']);
            }
            return $info;
        } else {
            return false;
        }
    }

    /**
     * 查看专业成员:根据专业id，获取该专业下面的所有人员细节信息:用户id、用户名、用户编号(学号)、专业id、专业名、专业排序(sign)
     * @author 陈博文 
     * @param id    专业id
     * @param row   每页显示条数
     * @param page  显示第几页
     */
    public function queProfessionPersonDetail($data)
    {
        $proUser = new ProfessionUser();
        $configPage['list_rows'] = config('paginate.list_rows');
        isset($data['row']) && $data['row'] > 0 && $configPage['list_rows'] = $data['row'];
        $configPage[config('paginate.var_page')] = 1;
        isset($data['page']) && $data['page'] > 0 && $configPage[config('paginate.var_page')] = $data['page'];

        //查询某一个专业下的所有用户id：$UserID[]
        $UserId = $proUser->where('profession',$data['id'])->field('user')->select();      
        foreach ($UserId as $v) {
            $UserID[] = $v['user'];
        }
        //查询用户的：用户id、用户名、用户编号(学号)、专业id、专业名、专业排序(sign)
        $join = [
            ['__PROFESSION_USER__ proUser','proUser.profession = pro.id'],
            ['__USER__ user','user.id = proUser.user']
        ];
        $ProMes = Db('profession')
        ->alias('pro')
        ->join($join)
        ->where('proUser.user','in',$UserID)
        ->field('user.id,user.username,user.idnumber,proUser.profession,pro.name,proUser.sign')
        ->paginate(['list_rows' => $data['row'], 'page' => $data['page']])->toArray();
        $data = $ProMes['data'];
        unset($ProMes['data']);
        $page = $ProMes;
        $ProMes = $data;
        for ($i=0; $i < count($ProMes); $i++) { 
            for ($j=0; $j < count($ProMes); $j++) { 
                if($ProMes[$i]['id'] == $ProMes[$j]['id']){
                    $new_arr[$ProMes[$i]['id']]['username'] = $ProMes[$i]['username']; 
                    $new_arr[$ProMes[$i]['id']]['idnumber'] = $ProMes[$i]['idnumber']; 
                    for ($k = 1; $k <= 3 ; $k++) { 
                        if($ProMes[$i]['sign'] == $k){
                        $sign[$i][$ProMes[$i]['profession']] = $ProMes[$i]['name'];
                        $new_arr[$ProMes[$i]['id']]['pro'][$ProMes[$i]['sign']] = $sign[$i];
                        }     
                    }                   
                }
            }
        }
        $new_arr['page'] = $page;       
        return getBack(0,$new_arr);        
    }
    /**
     * 显示用户信息1:查询此用户所有机构下的有效学生用户，实现分页，学号排序。
     * @author 陈博文
     * @param id            前端传入的用户id
     */
    public function queStuUser($data)
    {
        $RoleUser = new RoleUser();
        $organization = new Organization();
        $Role = new Role();
        $user = new User();
        $org_id = $user->where('id',$data['uuid'])->value('pid');
        $org_childrenIds = $organization->getChildren($org_id);
        //查询学生用户的：用户id、用户名、用户编号(学号)、专业id、专业名、专业排序(sign)
        $join = [
            ['__PROFESSION_USER__ proUser','proUser.profession = pro.id'],
            ['__USER__ user','user.id = proUser.user'],
            ['__ROLE_USER__ roleUser','roleUser.user_id = user.id'],
            ['__ROLE__ role','role.id = roleUser.role_id']
        ];
        $ProMes = Db('profession')
        ->alias('pro')
        ->join($join)
        ->where('user.pid','in',$org_childrenIds)
        ->where('role.name','student')
        ->field('user.id,user.username,user.idnumber,proUser.profession,pro.name,proUser.sign')
        ->order('idnumber')
        ->paginate(['list_rows' => $data['row'], 'page' => $data['page']])->toArray();
        $data = $ProMes['data'];
        unset($ProMes['data']);
        $page = $ProMes;
        $ProMes = $data;
        for ($i=0; $i < count($ProMes); $i++) { 
            for ($j=0; $j < count($ProMes); $j++) { 
                if($ProMes[$i]['id'] == $ProMes[$j]['id']){
                    $new_arr[$ProMes[$i]['id']]['username'] = $ProMes[$i]['username']; 
                    $new_arr[$ProMes[$i]['id']]['idnumber'] = $ProMes[$i]['idnumber']; 
                    for ($k = 1; $k <= 3 ; $k++) { 
                        if($ProMes[$i]['sign'] == $k){
                        $sign[$i][$ProMes[$i]['profession']] = $ProMes[$i]['name'];
                        $new_arr[$ProMes[$i]['id']]['pro'][$ProMes[$i]['sign']] = $sign[$i];
                        }     
                    }                   
                }
            }
        }
        $new_arr['page'] = $page;     
        return getBack(0,$new_arr);       
    }
    /**
     * 显示用户信息2:根据用户输入的信息模糊查询用户数据，依然是学生角色
     * @author 陈博文
     * @param id            前端传入的用户id
     */
    public function queStuUser_Like($where,$data)
    {
        $RoleUser = new RoleUser();
        $organization = new Organization();
        $Role = new Role();
        $user = new User();
        if($data['code'] != null){
            $where['pro.code'] = ['like','%'.$data['code'].'%'];
        }
        if($data['name'] != null){
            $where['pro.name'] = ['like','%'.$data['name'].'%'];
        }        
        //查询学生用户的：用户id、用户名、用户编号(学号)、专业id、专业名、专业排序(sign)
        $join = [
            ['__PROFESSION_USER__ proUser','proUser.profession = pro.id'],
            ['__USER__ user','user.id = proUser.user'],
            ['__ROLE_USER__ roleUser','roleUser.user_id = user.id'],
            ['__ROLE__ role','role.id = roleUser.role_id']
        ];
        $ProMes = Db('profession')
        ->alias('pro')
        ->join($join)
        ->where($where)
        ->where('role.name','student')
        ->field('user.id,user.username,user.idnumber,proUser.profession,pro.name,proUser.sign')
        ->order('user.idnumber desc')
        ->paginate(['list_rows' => $data['row'], 'page' => $data['page']])->toArray();  
        $data = $ProMes['data'];
        unset($ProMes['data']);
        $page = $ProMes;
        $ProMes = $data;
        for ($i=0; $i < count($ProMes); $i++) { 
            for ($j=0; $j < count($ProMes); $j++) { 
                if($ProMes[$i]['id'] == $ProMes[$j]['id']){
                    $new_arr[$ProMes[$i]['id']]['username'] = $ProMes[$i]['username']; 
                    $new_arr[$ProMes[$i]['id']]['idnumber'] = $ProMes[$i]['idnumber']; 
                    for ($k = 1; $k <= 3 ; $k++) { 
                        if($ProMes[$i]['sign'] == $k){
                        $sign[$i][$ProMes[$i]['profession']] = $ProMes[$i]['name'];
                        $new_arr[$ProMes[$i]['id']]['pro'][$ProMes[$i]['sign']] = $sign[$i];
                        }     
                    }                   
                }
            }
        }
        $new_arr['page'] = $page; 
        return getBack(0,$new_arr);
    }
    

    /**
     * 更新专业成员:通过前端传入用户id和专业ids，sign的数组来更新[专业-用户关系表]中的数据
     * 对于有多个专业的用户，能进行用户的专业修改，即能将第一专业修改为第二专业，所有的标记在用户专业关系表中
     * @author 陈博文
     * @param  id            前端传入的用户id
     * @param  ids           前端传入的所有专业id字段数组
     * @param  signs         前端传入的所有sign字段的数组
     */
    
    public function upProfessionUser($data)
    {
       /* $items = array( //测试数据
    1 => array('id' => 123,'sign' => 3),
    2 => array('id' => 456,'sign' => 2),
    3 => array('id' => 789,'sign' => 1)
    );*/
        $proUser = new ProfessionUser();
        $arr = array();
        foreach ($data['ids'] as $v) {
            $arr[] = $v;
        }
        for($i = 0; $i < count($arr); $i++){
            $proUser->where(['user' => $data['id'],'profession' => $arr[$i]['id']])->update(['sign'=>$arr[$i]['sign']]);
        }
        $res = $proUser->where('user',$data['id'])->select();//更新后当前用户下的所有专业信息
        if($res){
            return getBack(0,$res);
        }else{
            return getBack(1,'当前用户没有专业信息！');
        }
    }
    /**
     * 根据机构id得到机构下的所有专业以及该机构下的所有机构信息
     * @param array $data ['org_id' => '', 'uid' => '用户id']
     */
    public function getOrgAndPro($data) {
        $user = new User();
        $org  = new Organization();
        if (empty($data['org_id'])) {
            $org_id = $user->getOrgInfo($data['uid']);
        } else {
            $org_id = $data['org_id'];
        }
        $orgs = $org->queMessageChild_part($org_id,'id, name, haschild');
        $pro = $this->getInfoForOrg($org_id, 'id, name, code, describe');
        return ['org' => $orgs, 'pro' => $pro];
    }
    /**
     * 根据专业id得到专业详细信息
     * @param array $data ['uid' => '用户id'， 'pro_id' => '专业id']
     */
    public function getProInfo($data) {
        $user = new User();
        $org  = new Organization();
        if (empty($data['pro_id'])) {
            echo getBack(1, '缺少请求参数');
            exit;
        }
        $user_org_id = $user->getOrgInfo($data['uid']);
        $pro_info = $this->where(['status' => 0, 'id' => $data['pro_id']])->find();
        $org_info = $org->getProtionOrg($user_org_id, $pro_info->organization, 'name');
        return ['pro' => $pro_info, 'org' => $org_info];
    }
    /**
     * 批量查询
     * @author 李桐
     * @param id   传入的机构id
     */
    public function queAll($professionId){
        return $this->where('id','in',$professionId)->where('status','0')->select();
    }
    /**
     * 根据需求请求不同的方法
     * @param array $data 传入数据
     * @param string $name 请求的方法名
     */
    public function quePort($data, $name) {
        switch ($name) {
            case 'getInfoForOrg':
                if (empty($data['id'])) {
                    $user = new User();
                    $org_id = $user->getOrgInfo($data['uid']);
                } else {
                    $org_id = $data['id'];
                }
                $result = $this->getInfoForOrg($org_id, 'id, name');
                if (!$result) {
                    return '暂无专业！';
                }
                return $result;
                break;
            default:
                # code...
                break;
        }
    }
}


