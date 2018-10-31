<?php
namespace app\service\model;
use think\Model;

class Courseware extends Model
{
    protected function initialize() {
        parent::initialize();
    }
    protected $pk = 'id';

    //课件新增id自动完成
    protected $auto = ['id'];

    protected function setIdAttr($val) {
        return $val ? $val : createGuid();
    }
    /*
     * 添加课件信息
     */
    public function addWare($data) {
        $attachments = new Attachments();
        //保存附件
        $result_info = $attachments->saveFileBatch($data['upload_files'], $data['upload_files_hashs']);
        $save_data = [];
        foreach ($data['upload_files'] as $val) {
            $arr = ['name' => $val['name'], 'summary' => $val['summary'], 'course' => $data['course'], 'type' => $val['file']->getInfo('type'), 'attachments' => $result_info[$val['hash']], 'timecreated' => time(), 'author' => $data['uid'], 'status' => 0, 'flag' => $data['flag']];
            if (@!empty($data['course_sections'])) {
                $arr['course_sections'] = $data['course_sections'];
            }
            $save_data[] = $arr;
        }
        //批量新增
        $save_status = $this->allowField(true)->saveAll($save_data);
        if ($save_status) {
            return getBack(0, '添加成功！');
        } else {
            return getBack(1, '添加失败！');
        }
    }
    /*
     * 删除课件信息
     * @param string type 根据课程还是根据章节
     * @param string|array data id集合,课程为string，章节为数字索引的数组或字符串
     */
    public function delWare1($data, $type = false) {
        $attachments = new Attachments();
        if ($type == 'course') {//课程
            $attachments_id = collection($this->where(['course' => $data, 'status' => 0])->field('attachments')->select())->toArray();
            //删除课件
            $result = $this->where('course', $data)->update(['status' => 1, 'timemodified' => time()]);
        } elseif ($type == 'section') {//章节
            $attachments_id = collection($this->where('course_sections', 'in', $data)->where('status', 0)->field('attachments')->select())->toArray();
            //删除课件
            $result = $this->where('course_sections', 'in', $data)->update(['status' => 1, 'timemodified' => time()]);
        } elseif (!$type) {
            $info = $this->where(['id' => $data, 'status' => 0])->field('attachments')->find();
            if ($info) {
                $attachments_id[] = $info->toArray();
            }
            $result = $this->where('id', $data)->update(['status' => 1, 'timemodified' => time()]);
        }
        if ($result !== false) {
            if ($attachments_id) {
                array_walk($attachments_id, function(&$val, $key){
                    $val = $val['attachments'];
                });
                if ($attachments->updateFile($attachments_id)) {
                    return getBack(0, '成功!');
                } else {
                    return getBack(1, '删除失败！');
                }
            }
            return getBack(0, '成功!');
        } else {
            return getBack(1, '删除文件失败！');
        }
    }
    /*
     * 查询课件
     * @param array data ['id' => 课程id， 'section' => 章节id（可传可不传）]
     */
    public function queWare($data) {
        $where = ['w.course' => $data['id'], 'w.status' => 0, 's.status' => 0, 'c.status' => 1];
        if (@!empty($data['section'])) {
            $where['w.course_sections'] = $data['section'];
        }
    //    $field = 'w.id, w.name, w.summary, w.type as filetype, w.attachments, w.flag, c.fullname as coursename, s.name as sectionname, s.type';
        $field = 'w.id, w.name, w.summary, w.attachments as path, w.type';
        $join = [
            ['__COURSE__ c', 'c.id = w.course'],
            ['__COURSE_SECTIONS__ s', 's.id = w.course_sections']
        ];
        $info = $this->alias('w')->join($join)->where($where)->field($field)->select();
        if ($info) {
            return collection($info)->toArray();
        } else {
            return false;
        }
    }
    
    
}