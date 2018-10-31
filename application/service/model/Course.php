<?php
namespace app\service\model;
use app\service\controller\CourseCon;
use app\service\model\Attachments;
use think\Model;
use think\Loader;
class Course extends Model
{
    protected static function init() {
        Course::event('before_insert', function ($data, $pic = array()) {
            $attachments = new Attachments();
            $data['timecreated'] = time();
            if (empty($data['parentstr'])) {
                $data['parentstr'] = config('parentstr_prefix');
            }
            if (!empty($data['upload_files'])) {
                $data['pic_path'] = $attachments->saveFile($data['upload_files'][0]['hash'], $data['upload_files'][0]['file']);
            }
        });
    }
    protected function initialize() {
        parent::initialize();
    }
    protected $pk = 'id';
    //课程新增id自动完成
    protected $auto = ['id'];

    protected function setIdAttr($id) {
        return $id ? $id : createGuid();
    }
    /**
     * 删除指定目录下的空目录及其空子目录
     * @param $path 要删除的空目录的最上级目录
     */
    public function delCatalog($path) {
        if( is_dir($path) && ($handle = opendir($path) ) !== false) {
            while( ($file = readdir($handle) ) !== false) {     // 遍历文件夹
                if( $file != '.' && $file != '..' ) {
                    $curfile = $path.'/'.$file;          // 当前目录
                    if( is_dir($curfile) ) {                // 目录
                        $this->delCatalog($curfile);          // 如果是目录则继续遍历
                        if( count(scandir($curfile)) == 2 ) { // 目录为空,=2是因为. 和 ..存在
                            rmdir($curfile);             // 删除空目录
                        }
                    }
                }
            }
            closedir($handle);
        }
    }
    /**
     * 检查同机构课程名称是否相同
     * @param $data 课程数据
     * @return string
     */
    public  function checkName($data, $type = 'add') {
        $all_courses = [];
        if ($type === 'add') {
            $all_courses = $this->where(['status'=>1, 'department'=>$data['department']])->field('fullname, idnumber, shortname')->select();
        } else if ($type === 'update') {
            $all_courses = $this->where('id', '<>', $data['id'])->where(['status'=>1, 'department'=>$data['department']])->field('fullname, idnumber, shortname')->select();
        }
        foreach ($all_courses as $val) {
            if ($val['fullname'] === $data['fullname']) {
                echo getBack(1, '同一机构课程全称重复');
                exit;
            } else if ( $val['shortname'] === $data['shortname']) {
                echo getBack(1, '同一机构课程简称重复');
                exit;
            } else if ($val['idnumber'] === $data['idnumber']) {
                echo getBack(1, '当前存在此课程编号！');
                exit;
            }
        }
    }
    /**
     * 数据合法性检测
     * @param $file 文件
     * @param $data 课程数据
     * @return string
     */
    public function  checkData($file, $data, $type = 'add'){
        $validate = Loader::validate('Course');
        if ($type === 'add') {
            if ( (!$validate->scene('file')->check($file) ) || (!$validate->scene('data')->check($data)) ) {
                echo getBack(1, $validate->getError());
                exit;
            }
        } else if ($type === 'update') {
            if ( (!$validate->scene('file')->check($file) ) || (!$validate->scene('update')->check($data)) ) {
                echo getBack(1, $validate->getError());
                exit;
            }
        }
    }
    /**
     * 保存课程
     * @param $data 课程数据
     * @return string
     */
    public function saveCourse($data){
        //有前置操作
        $res = $this ->allowField(true)->save($data);
        if($res) {
            return getBack(0,'添加课程成功' );
        } else{
            return getBack(1,'添加课程失败');
        }
    }
    /**
     * 更新课程数据
     * @param $data 课程数据
     * @return string
     */
    public function updataCourse($data) {
        //处理课程图片
        $data['timemodified'] = time();
        $attachments = new Attachments();
        $course_info_old = $this->where(['id' => $data['id'], 'status' => 1])->find();
        if(!empty($data['upload_files'])) {
            $pic_id_new = $attachments->searchFile($data['upload_files'][0]['hash']);
            if ($pic_id_new == $course_info_old['pic_path']) {
                $data['pic_path'] = $pic_id_new;
            } else {
                //保存新图片
                $data['pic_path'] = $attachments->saveFile($data['upload_files'][0]['hash'], $data['upload_files'][0]['file']);
                //修改旧图片引用次数减一
                $attachments->updateFile($course_info_old['pic_path']);
            }
        } else {
            $data['pic_path'] = '';
            //修改旧图片引用次数减一
            $attachments->updateFile($course_info_old['pic_path']);
        }
        $result = $this->allowField(true)->save($data, ['id' => $data['id']]);
        if ($result) {
            return getBack(0, '修改课程成功!' );
        } else {
            return getBack(1, '修改课程失败!');
        }
    }
    /**
     * 删除课程
     * @param $id 课程id
     */
    public function delCourse($id) {
        $attachments     = new Attachments();
        $course_sections = new CourseSections();
        $courseware      = new Courseware();
        $course_info = $this->where(['id' => $id, 'status' => 1])->find();
        //图片引用次数减一
   //     $attachments->updateFile($course_info['pic_path']);
        //附件修改
        
        //课程章节修改
        $course_sections->delForCourseId($id);
        //课程信息修改删除
        $return = $this->where('id', $id)->update(['status' => 0, 'timemodified' => time()]);
        if ($return) {
            return getBack(0,'删除课程成功');
        } else {
            return getBack(1,'删除课程失败');
        }
    }
    /*
     * 查询本级课程，层级
     * id为空时，查询用户所在机构所有课程，不为空，只查询id课程
     * @param  $id  主键  课程id  查询当前课程
     * @param  $uid  整型  用户id  查询机构课程
     */
    public function queSelf($data) {
        $globalcourse = new Course();
        if ($data['id']) {
            $thiscourse = $course->where(['id' => $data['id'], 'status' => 1])->field('timemodified', 'timecreated', 'status', true)->select();
            if ($thiscourse > 0) {
                return getBack(0, $thiscourse);
            } else {
                return getBack(1, '没有找到该课程！');
            }
        }
        $user = new User(); 
        $id = $data['uid'];
        $thispid = $user->where('id', $id)->value('pid');
        $result = $globalcourse->where(['status' => 1, 'department' => $thispid])->select();
        $courses = null;
        for ($n = 0; $n < count($result); $n ++) {
            $courses[$n] = $result[$n]->data;
        }      
        $max = 0;
        for ($o = 0; $o < count($courses); $o ++) {
            $countpstr = count(explode(',', $courses[$o]['parentstr']))-1;
            if ($countpstr>$max) {
                $max = $countpstr;
            }
        }
        $newarray = array();
        for ($p = 1; $p <= $max; $p ++) {
            $newarray[$p] = array();
        }
        if ($courses > 0) {
            for ($i = 0; $i < count($courses); $i ++) {
                $countpstr = count(explode(',', $courses[$i]['parentstr']))-1;
                $newarray[$countpstr][] = $courses[$i];
            }
            for ($mm = count($newarray); $mm - 1 > 0; $mm--) {
                for ($m = 0;$m<count($newarray[$mm]);$m++) {
                    for ($q = 0; $q<count($newarray[$mm - 1]); $q++) {
                        if ($newarray[$mm][$m]['pid'] == $newarray[$mm - 1][$q]['id']) {
                            $newarray[$mm - 1][$q]['child'][]=$newarray[$mm][$m];
                            break;
                        }
                    }
                }
            }
            return getBack(0, $newarray[1]);
        } else {
            return getBack(1, '没有找到该机构的课程！');
        }
    }
    /*
     * 查询所有课程或分页查询课程
     * 无参，查询所有
     * @param $uid string 用户id
     * @param  $page       整型 查询页
     * @param  $list_rows  整型 每页显示行数
     * @param $where 数组 查询条件
     */
    public function queAllOrPage($uid, $page = null, $list_rows = null, $where = []) {
        $organizations    = new Organization();
        $user             = new User();
        $u_organization = $user->where(['id' => $uid, 'status' => 0])->value('pid');//用户所在机构id
        $ids_organization = $organizations ->getChildren($u_organization);//得到用户所在机构及以下所有机构id
        //查询条件
        $where['status'] = 1;
        if ($page) {
            $courseCategories = new CourseCategories();
            $cateInfo = collection($courseCategories->where(['department' => ['in', $ids_organization], 'visible' => 0])->field('id, name')->select())->toArray();
            //查询该用户权限范围内的某页的课程
            $configPage[config('paginate.var_page')] = $page < 1 ? 1 : $page;
            !$list_rows && $list_rows = config('paginate.list_rows');
            $configPage['list_rows'] = $list_rows;
            if (!empty($where['department'])) {
                //查询某一机构下的课程
                foreach($ids_organization as $val) {
                    if ($where['department'] == $val) {
                        $courseInfo = $this->where($where)->order('timecreated')->paginate($configPage, config('paginate.custom'))->toArray();
                        $page = array_slice($courseInfo, 0, 4);
                        array_splice($courseInfo, 0, 4);
                        $courseInfo['page'] = $page;
                        break;
                    } else {
                        $courseInfo = '';
                    }
                }
            } else {
                $courseInfo = $this->where($where)->where('department','in', $ids_organization)->order('timecreated')->paginate($configPage, config('paginate.custom'))->toArray();
                $page = array_slice($courseInfo, 0, 4);
                array_splice($courseInfo, 0, 4);
                $courseInfo['page'] = $page;
            }
            array_walk($courseInfo['data'], function(&$val, $key) use ($cateInfo) {
                foreach ($cateInfo as $v) {
                    if ($val['category'] == $v['id']) {
                        $val['category'] = $v['name'];
                    }
                }
            });
            $courseInfo['data'] = empty($courseInfo['data']) ? '' : $this->operation($courseInfo['data']);
        } else {
            //查询该用户权限范围内的所有课程
            $data = collection($this->where($where)->where('department','in', $ids_organization)->order('timecreated')->select())->toArray();
            $courseInfo = empty($data) ? '' : $this->operation($data);
        }
        return $courseInfo;
    }
    /*
     * 查询所有课程的图片
     * @param  $data       array 所有查询的一维数组
     * 返回所有课程数组
     */
    public function operation($data) {
        $attachments = new Attachments();
        $pic_path_arr = array();
        $pic_path_str = '';
        foreach ($data as $k => $v) {
            $pic_path_arr[$v['id']] = $v['pic_path'];
            $pic_path_str .= $v['pic_path'] . ',';
        }
        $pic_arr = $attachments->queAllFile($pic_path_str, $pic_path_arr);
        array_walk($data, function(&$val, $key, $arr){
            $value = $arr[$val['id']];
            $val['pic_path'] = "$value";
        }, $pic_arr);
        return $data;
    }
    /*
     * 查询所有课程的类型
     * @param  $data  所有查询的一维数组
     * 返回所有课程数组
     */
    /*public function courseCategory($data) {
        $org = new Organization();
        $co = new Course();
        $res = $co->whereOr('fullname','like',$data['fullname'])->value('department');
        var_dump($res);
        $uall_org = $org->where('pid','like',$res)->select();
        var_dump($uall_org);
    }*/
    /*
     * 得到某用户的所有课程，返回带课程类型和机构信息
     * @param array data ['uid' => 用户id, 'page' => 请求页面, 'rows' => 每页显示条数]
     */
    public function getCourseFc($data) {
        $org = new Organization();
        $user = new User();
        $u_org = $user->where(['id' => $data['uid'], 'status' => 0])->value('pid');//用户所在机构id
        $ids_org = $org ->getChildren($u_org);//得到用户所在机构及以下所有机构id
        //获取该用户所能查看的所有有效课程
        $join = [
            ['__ORGANIZATION__ org', 'cou.department = org.id'],
            ['__COURSE_CATEGORIES__ cate', 'cou.category = cate.id']
        ];
        if(empty($data['rows'])) {
            $rows = config('paginate.list_rows');
        } else {
            $rows = is_numeric($data['rows']) ? $data['rows'] : config('paginate.list_rows');
        }
        $configPage = [config('paginate.var_page') => $data['page'] < 1 ? 1 : $data['page'] , 'list_rows' => $rows];
        $c_datas = $this->alias('cou')->where(['cou.status' => 1, 'org.status' => 0, 'cate.visible' => 0])->where('cou.department', 'in', $ids_org)->join($join)->field('cou.id, cou.fullname, cou.shortname, cou.idnumber, cou.summary, cou.pic_path, cou.numsections, org.name as org_name, org.id as org_id, cate.id as cate_id, cate.name as cate_name')->paginate($configPage, config('paginate.custom'))->toArray();
        return getBack(0, $c_datas);
    }
    /*
     * 根据课程id得到课程信息
     * @param string $id 课程id
     * return 课程信息，机构id和名，类型id和名
     */
    public function getInfo ($id = null) {
        $join = [
            ['__ORGANIZATION__ org', 'org.id = cou.department'],
            ['__COURSE_CATEGORIES__ cate', 'cou.category = cate.id']
        ];
        $info = $this->alias('cou')->where(['cou.status' => 1, 'org.status' => 0, 'cate.visible' => 0, 'cou.id' => $id])->join($join)->field('cou.id, cou.department as org_id, org.name as org_name, cou.category as cate_id, cate.name as cate_name, cou.fullname, cou.shortname, cou.idnumber, cou.summary, cou.pic_path, cou.numsections')->select();
        if ($info) {
            return $info;
        } else {
            return false;
        }

    }
}