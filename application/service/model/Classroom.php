<?php
namespace app\service\model;
use think\Model;
use app\service\validate\Validates;

class Classroom extends Model
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

    /**@author  叶文
     * 根据用户id查询某用户所有的课程 分页显示
     * @param $id 用户id
     * @param $page 显示某页
     * @param $list_rows 每页显示多少行
     * @return array 课程列表
     */
    public function userAllClassroom($id, $page = null, $list_rows = null) {
        !$page && $page = 1;
        $configPage[config('paginate.var_page')] = $page < 1 ? 1 : $page;
        !$list_rows && $list_rows = config('paginate.list_rows');
        $configPage['list_rows'] = $list_rows;
        $user = new User();
        $join = [
            ['__ROLE_USER__ ru', 'ru.user_id = user.id'],
            ['__ROLE__ r', 'r.id = ru.role_id'],
            ['__ORGANIZATION__ org', 'org.id = user.pid']
        ];
        $where = ['user.status' => 0, 'r.status' => 1, 'user.id' => $id, 'org.status' => 0];
        $field = ['user.id, user.pid, r.level, org.parentstr'];
        $userInfo = $user->alias('user')->where($where)->join($join)->field($field)->find();
        if (!$userInfo) {
            return getBack(1, '当前用户不存在或用户信息错误！');
        }
        $join = [
            ['__ORGANIZATION__ org', 'org.id = c.department'],
            ['__USER__ user', 'user.id = c.authid'],
            ['__COURSE_CATEGORIES__ cc', 'cc.id = c.category']
        ];
        $field = 'c.id, c.fullname, c.shortname, c.idnumber, c.summary, c.classroompic, user.realname as auth, cc.name as category, org.name as department, c.semester, c.numsections';
        if ($userInfo->level <= 3) {//该用户为管理员
     /*       $join = [
                ['__ORGANIZATION__ org', 'org.id = c.department'],
                ['__USER__ user', 'user.id = c.authid'],
                ['__COURSE_CATEGORIES__ cc', 'cc.id = c.category']
            ];*/
            $where = ['c.status' => 0, 'org.status' => 0];
    //        $field = 'c.id, c.fullname, c.shortname, c.idnumber, c.summary, c.classroompic, user.realname as auth, cc.name as category, org.name as department, c.semester, c.numsections';
            $info = $this->alias('c')->where($where)->where(function($q) use ($userInfo){
                $q->where('org.parentstr', 'like', $userInfo->parentstr . '%')->whereOr('org.id', $userInfo->pid);
            })->join($join)->field($field)->paginate($configPage, config('paginate.custom'));
            if ($info) {
                return getBack(0, $info);
            }
            return getBack(1, '暂无课程！');
        }
        $classroomUser = new ClassroomUser;
        $classroomid = collection($classroomUser->where('user_id', $id)->select())->toArray();
        if ($classroomid) {
            $ids = array();
            foreach ($classroomid as $v){
                $ids[] = $v['classroom_id'];
            }
            $classroom = $this->alias('c')->where(['org.status' => 0, 'c.id' => ['in', $ids], 'org.status' => 0])->join($join)->field($field)->paginate($configPage, config('paginate.custom'));
            if ($classroom) {
                return getBack(0, $classroom);
            } else {
                return getBack(1, '当前暂无课堂信息！');
            }
        } else {
            return getBack(1, '当前暂无课堂信息！');
        }
    }
    /**@author  陈博文
     * 根据课程id添加课程到课堂中(分两种情况:前端传入课程id和未传入课程id),并修改相应的数据表的信息
     */
        //用户选中课程信息前端发送一个课程id,找到课程信息存入课堂表中，返回$res到前端
    public function TakeForCourse($data){
        $validates = new Validates('classroom');
        $attachments = new Attachments();
        if (!$validates->check($data)) {
            return getBack(1, $validates->getError());
        }
        $data['authid'] = $data['uid'];
        $data['timecreated'] = time();
        $data['status'] = 0;
        $result = $sectionsInfos = $coursewareInfo = false;
        if (@!empty($data['course_id'])) {//继承的课程
            $course = new Course();
            $result = $course->where(['id' => $data['course_id'], 'status' => 1])->find();//根据id查询此id对应的课程信息
            if (!$result) {
                echo getBack(1, '原课程不存在！');
                exit;
            }
            $attachments->addFile($result->pic_path);//图片使用次数加1
            $data['classroompic'] = $result->pic_path;
            //章节
            $sections = new CourseSections();
            $sectionsInfos = collection($sections->where(['courseid' => $data['course_id'], 'status' => 0])->field('id as course_sections, pid, parentstr, type, section, name, summary, sort')->order('sort')->select())->toArray();
            //课件
            $courseware = new Courseware();
            $coursewareInfo = collection($courseware->where(['course' => $data['course_id'], 'status' => 0])->field('name, summary, course_sections, type, attachments, flag')->select())->toArray();
        }
        if(!empty($data['upload_files'])) {
            $files = end($data['upload_files']);
            $data['classroompic'] = $attachments->saveFile($files['hash'], $files['file']);
            if (!$result) {
                $attachments->updateFile($result->pic_path);//图片使用次数减1
            }
        }
        $info = $this->data($data)->allowField(true)->save();
        $classroomId = $this->id;
        if (!$info) {
            return getBack(1, '创建课堂失败');
        }
        $isTrue = false;
        $msg = '';
        //根据课程的章节和课件，创建课堂的章节和课件
        if ($sectionsInfos) {
            $classroomSections = new ClassroomSections();
            $section = $sections = [];
            foreach ($sectionsInfos as $value) {
                $value['classroomid'] = $classroomId;
                $value['status'] = 0;
                $value['timecreated'] = time();
                if ($value['type'] == 1) {
                    $sections[] = $value;
                } else {
                    $section[] = $value;
                }
            }
            //保存课堂章信息
            $section = $classroomSections->allowField(true)->saveAll($section);
            if ($section) {
                foreach ($sections as $key => $value) {
                    foreach ($section as $v) {
                        if ($value['pid'] = $v->course_sections) {
                            $sections[$key]['pid'] = $v->id;
                            $sections[$key]['parentstr'] = $v->parentstr . $v->id;
                        }
                    }
                }
                //保存课堂节信息
                $sections = $classroomSections->allowField(true)->saveAll($sections);
                if (!$sections) {
                    $isTrue = true;
                    $msg = '创建课堂成功，保存课堂节信息失败！';
                }
            } else {
                $isTrue = true;
                $msg = '创建课堂成功，保存课堂章失败！';
            }
        }
        $files = [];
        if ($coursewareInfo) {
            $classroomCourseware = new ClassroomCourseware();
            foreach ($coursewareInfo as $key => $value) {
                $files = $value['attachments'];
                $coursewareInfo[$key]['classroom'] = $classroomId;
                $coursewareInfo[$key]['author'] = $data['uid'];
                $coursewareInfo[$key]['status'] = 0;
                $coursewareInfo[$key]['timecreated'] = time();
                if ($value['flag'] == 1) {
                    $isOk = false;
                    foreach ($section as $k => $v) {
                        if ($value['course_sections'] == $v->course_sections) {
                            $coursewareInfo[$key]['classroom_sections'] = $v->id;
                            $isOk = true;
                        }
                    }
                    if ($isOk) {
                        continue;
                    }
                    foreach ($sections as $k => $v) {
                        if ($value['course_sections'] == $v->course_sections) {
                            $coursewareInfo[$key]['classroom_sections'] = $v->id;
                        }
                    }
                }
            }
            //保存课程的课件到课堂
             $resutl = $classroomCourseware->allowField(true)->saveAll($coursewareInfo);
             if (!$result) {
                if ($isTrue) {
                    $msg .= '保存课堂课件失败！';
                } else {
                    $isTrue = true;
                    $msg = '创建课堂成功，保存课堂课件失败！';
                }
             }
        }
        if ($files) {
            //引用了的课件次数加1
            $attachments->addFile($files);//图片使用次数减1
        }
        if ($isTrue) {
            return getBack(0, $msg);
        }
        return getBack(0, '创建课堂成功！');
    }
    /**@author  陈博文
     * 通过课堂id更新课堂信息
     */
    public function updateClassroomMsg($data)
    {
        if (@!empty($data['file'])) {
            $attachments = new Attachments();
            $info_id = $attachments->saveFile($data['file_hash'],$data['file']);//前端上传了新的图片,返回图片id
            $data['classroompic'] = $info_id;
        }
        $result = $this->allowField(true)->validate('Classroom.edit')->isUpdate(true)->save($data);
        if ($result === false) {
            return getBack(1, $this->getError());
        }
        return getBack(0, '编辑成功！');
    }
    /**@author  叶文
     * 通过课堂id获取课堂相关信息
     * @param $id
     * @return array  课堂信息
     */
    public function CourseMsg($id) {
        $class = new Classroom;
        $res = $class ->where(['id'=>$id,'status'=>0])->field('id,fullname,shortname,idnumber,summary,authid,classroompic,course_id,category,department,semester,numsections')->find();
        if($res) {
        //    $attachments = new Attachments;
            $user = new User;
            $organization = new Organization;
            $categories = new CourseCategories;
        //    $picid = $res['classroompic'];
        //    $picpath = $attachments->where('id',$picid)->field('path')->find();
            $type = $categories->where(['id'=>$res['category'], 'visible'=>0])->field('name')->find();
            $auth = $user->where('id', $res['authid'])->field('username')->find();
            $department = $organization->where(['id'=>$res['department'], 'status'=>0])->field('name')->find();
            $res['department'] = $department['name'];
        //    $res['classroompic'] = $picpath['path'];
            $res['category'] = $type['name'];
            $res['auth'] = $auth['username'];
            $date = $this->sectionsTree($res['course_id']);
            $res['sections'] = $date;
            unset($res['authid']);
        }
        return $res;
    }
    /**@author  叶文
     * 通过课堂id获取该课堂章节树形数组
     * @param $classid 课堂id
     * @return array 章节树形数组
     */
    public function sectionsTree($classid) {
        $sections = new ClassroomSections;
        $sectionres = $sections->where(['classroomid'=>$classid, 'status'=>0])->select();
        $arr = $this->getChild($sectionres);
        return $arr;
    }
    /**@author  叶文
     * 构造树形数组
     * @param $sectionres 同一课堂所有章节数组
     * @param string $pid
     * @return array 章节树形数组
     */
    public function getChild($sectionres, $pid='0') {
        $array = array();
/*        $attachment = new Attachments;*/
        foreach ($sectionres as $k => $v) {
            if ( $v['pid'] == $pid ) {
                $v['children'] = self::getChild($sectionres, $v['id']);
/*                $file = $attachment->where('id', $v['file'])->field('path')->find();
                $v['file'] = $file['path'];*/
                $array[] = $v;
            }
        }
        return $array;
    }
    /**@author  李桐
     *方法详情
     *删除课堂
     *@param 参数类型 参数名  参数用途 
     *@param char(32) Id       定位删除信息
     */
    public function delClassroom($data){
        $this->save(['status' => '1', 'timemodified' => time()],['id' => $data['id']]);
        //附件引用次数减一
        $this->delAttachments($data);
        $classroomSec = new ClassroomSections();
        $classroom_sections = $classroomSec->classroomSectionsFind($data['id']);
        $classroomSec->delClassroomSections($data['id']);
        //通过classroom_sections来删除表classroomCourseware中的信息
        $classroomCou = new ClassroomCourseware();
        $res = $classroomCou->delClassroomCourseware($classroom_sections);
        $res = $this->delClassroomUser($data);
        if($res){
            return getBack(0,"1");
        }else{
            return getBack(1,"error");
        }
    }
    /**@author  李桐
     *方法详情
     *通过id删除表classroom_sections与表classroom_courseware中对应的数据，并使相应的附件引用次数减一
     *@param 参数类型 参数名               参数用途 
     *@param char(32) IdClassroomSec       表Classroom_sections中的id用来，定位删除信息
     */
    public function delIdClassroomSec($data){
        //删除Classroom_sections中对应的数据
        $classroomSec = new ClassroomSections();
        $list = $classroomSec->delIdClassroomSec($data['IdClassroomSec']);
        //删除Classroom_courseware中对应的数据
        $classroomCou = new ClassroomCourseware();
        $classroomCou->delClassroomCourseware($list['id']);
        $attachments = new Attachments();
        $res = $attachments->updateFile($list['file']);
        if($res){
           return getBack(0,"1");
        }else{
           return getBack(1,"error");
        }
    }
    /**@author  李桐
     *方法详情
     *通过课程id使表attachments中附件引用次数减一
     *@param 参数类型 参数名  参数用途 
     *@param char(32) Id       定位删除信息
     */
    public function delAttachments($data){
        $classroomSec = new ClassroomSections();
        $file = $classroomSec->fileFind($data['id']);
        $attachments = new Attachments();
        $res = $attachments->updateFile($file);
        if($res){
            return getBack(0,"1");
        }else{
            return getBack(1,"error");
        }
    }
    /**@author  李桐
     *方法详情
     *删除表classroom_user中对应的数据
     *@param 参数类型 参数名  参数用途 
     *@param char(32) Id或者user_id       定位删除信息
     */
    public function delClassroomUser($data){
        $classroomUser = new ClassroomUser();
        $res = $classroomUser->delClassroomUser($data);
        if($res){
            return getBack(0,"1");
        }else{
            return getBack(1,"error");
        }
    }
    /**@author  李桐
     *方法详情
     *通过课堂id获取课程id
     *@param 参数类型 参数名  参数用途 
     *@param char(32) id       获取课程id
     */
    public function getCourseid($id){
        $res = $this->where('id',$id)->where('status',0)->field('course_id')->find();
        return $res['course_id'];
    }

}