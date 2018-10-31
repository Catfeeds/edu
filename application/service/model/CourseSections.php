<?php
namespace app\service\model;

use think\Model;
use app\service\model\Attachments;

class CourseSections extends Model
{
    protected $auto = [];
    protected $insert = ['id'];
    protected $update = [];
    protected $readonly = ['timecreated'];//只读字段

    protected function setIdAttr($value) {
        return $value ? $value : createGuid();
    }

    /*
     *--------------------------新增-------------------------- 
     *@param 类型			参数名			参数说明
     *@param tinyint(1)		type			判断内容是章还是节
     *@param char(32)		courseid		课程id
     *@param  text			file			确定附件id
     *@param varchar(255)	name			名称
     *@param text			summary			摘要
     *@param int(10)		sort			排序序号
     *@param bigint(10)		section			课时（章才有，节没有）
     *@param char(32)		pid				父Id（节才有，章没有）
     */
    public function add($data){
        $where = ['status' => 0, 'courseid' => $data['courseid']];
        $allData = $this->where($where)->select();
        foreach ($data[config('sections_param')] as $key => $value) {
            if ($this->operationInfo('sort', $allData, empty($data['pid']) ? '0' : $data['pid'], $data['type'], $value['sort'])) {
              return getBack(1,'该数据排序序号已经存在！');
            }
            if ($data['type'] == '1') {
                $list[$key]['parentstr'] = $this->operationInfo('getParent', $allData, $data['pid']) . $data['pid'] . ',';
                $list[$key]['section']       = 0;
            } else {
                $list[$key]['parentstr'] = config('parentstr_prefix');
                $list[$key]['section']       = $value['section'];
            }
            if (@empty($data['pid'])) {
                $data['pid'] = config('pid_default');
            }
            $list[$key]['pid']         = $data['pid'];
            $list[$key]['type']        = $data['type'];
            $list[$key]['courseid']    = $data['courseid'];
            $list[$key]['name']        = $value['name'];
            $list[$key]['summary']     = $value['summary'];
            $list[$key]['sort']        = $value['sort'];
            $list[$key]['timecreated'] = time();
        }
        if ($info = $this->allowField(true)->saveAll($list)) {
            $number = 0;
            $count = count($info);
            while ($number < $count) {
                $reData[$number] = $info[$number]->hidden(['parentstr', 'timecreated'])->toArray();
                $number++;
            }
            return getBack(0, $reData);
        } else {
            return getBack(1, '内部错误，请联系相关技术人员！');
        }
/*
        $num = count($data[config('sections_param')]);
        $i = 0;
        while($i < $num){
            $list[$i]['courseid'] = $data['courseid'];
            $list[$i]['type']     = $data['type'];
            $list[$i]['name']     = $data[config('sections_param')][$i]['name'];
            $list[$i]['summary']  = $data[config('sections_param')][$i]['summary'];
            $list[$i]['sort']     = $data[config('sections_param')][$i]['sort'];
            //判断新增的内容是章还是节
            if($data['type'] == 0){
                //为章，则还需要section
                $list[$i]['section'] = $data[config('sections_param')][$i]['section'];
            }else{
                //为节，则还需要pid
                $list[$i]['pid'] = $data['pid'];
            }
            $i = $i+1;
        }
        $i = 0;
        while($i < $num){
            $sections = new CourseSections($list[$i]);
            $sort = $sections->where('status','=',0)->where('type','=',$list[$i]['type'])->where('courseid','=',$list[$i]['courseid'])->where('sort','=',$list[$i]['sort'])->find();
            //判断新增的sort是否存在
            if($sort == NULL){
                //判断新增内容是章还是节
                if($list[$i]['type']){
                    $res = $sections->where('status','=',0)->where('id','=',$list[$i]['pid'])->find();
                    $str = $res['parentstr']." ".$res['id'];
                    $sections->parentstr   = $str;
                }else{
                    $sections->parentstr   = config('parentstr_prefix');
                }
                $sections->status      = '0';
                $sections->timecreated = time();
                $sections->allowField(true)->save();
                $id = $sections->where('status','=',0)->where('sort','=',$list[$i]['sort'])->find();
                //将新插入的数据存入数组ret中，用于返回前端
                $ret[$i]['id']       = $id['id'];
                $ret[$i]['type']     = $id['type'];
                $ret[$i]['courseid'] = $id['courseid'];
                $ret[$i]['section']  = $id['section'];
                $ret[$i]['name']     = $id['name'];
                $ret[$i]['summary']  = $id['summary'];
                $ret[$i]['sort']     = $id['sort'];
            }else{
                return getBack(1,'0');
            }
            $i = $i+1;
        }
        //返回id、type、courseid、section、name、summary、sort
        if($ret){
            return getBack(0,$ret);
        }else{
            return getBack(1, '0');
        }*/
    }
    /*
     *---------------------修改---------------------
     *需要id、courseid、type、sort即其他需要修改的信息
     *@param 类型		参数名		参数说明
     *@param char(32)	id			用于确定其在表中的位置
     */
    public function up($data){
        $this->timemodified = time();
        $result = $this->allowField(true)->save($data,['id' => $data['id']]);
        if($result){
            return getBack(0,'更新成功！');
        }else{
            return getBack(1,'更新失败！');
        }
    }
    /*
     *-----------------查询------------------
     *@param 类型		参数名		参数说明
     *@param char(32)	courseid	用于确定课程 
     */
    public function que($data){
        //找出所有符合条件的章和节	
        $res = collection($this->where('status', 0)->where('courseid', $data['courseid'])->field('id, pid, name, summary, sort, type, section')->order('sort')->select())->toArray();
        if(!$res){
            return getBack(1, '无任何数据！');
        }else{
            $result = getClassify_index($res, 'id', 'pid', config('pid_default'));
            return getBack(0, $result);
        }
    }
    /*
     *-----------------------删除-----------------------
     *@param 类型			参数名		参数说明
     *@param char(32)		id			用于确定其在表中的位置
     *@param tinyint(1)		type		判断内容是章还是节
     */
    public function del($data){
        if (@empty($data['id']) || @empty($data['type'])) {
            return getBack(1, '请求参数错误！');
        }
        $courseware = new Courseware();
        $this->timemodified = time();
        //判断是章还是节
        if($data['type'] == 1) {
            //为节，则只需要删除该条数据
            if ($this->save([ 'status'  => '1'],['id' => $data['id']]) !== false) {
                //删除节下面的所有课件
                $result = $courseware->delWare1($data['id'], 'section');
                return $result;
            } else {
                return getBack(1, '删除节失败！');
            }
        }else{
            //为章，或许该章下面所有节的附件
            $knobble_all = collection($this->where(['pid' => $data['id'], 'status' => 0])->field('id')->select())->toArray();
            array_walk($knobble_all, function(&$val, $key){
                $val = $val['id'];
            });
            $knobble_all[] = $data['id'];
            //删除章
            //为章，则还需要删除该章下面所有的节
            if ($this->save(['status' => 1], ['id' => $data['id']]) !== false) {
                if ($this->where('pid', $data['id'])->update(['status' => 1, 'timemodified' => time()]) !== false) {
                    //删除章节下面的所有课件
                    $result = $courseware->delWare1($knobble_all, 'section');
                    return $result;
                } else {
                    $this->save(['status' => 0], ['id' => $data['id']]);
                    return getBack(1, '删除节失败！');
                }
            } else {
                return getBack(1, '删除章失败！');
            }
         }
    }
    /*
     * 根据课程id删除所有章和节
     * 并同时删除其章和节的附件
     */
    public function delForCourseId($id) {
        $attachments = new Attachments();
        //删除章节，修改章节信息
        $this->where('courseid', $id)->update(['status' => 1, 'timemodified' => time()]);
        $all = collection($this->where('courseid', $id)->field('id, file')->select())->toArray();
        $file_ids = '';
        foreach ($all as $val) {
            $file_ids .= empty($val['file']) ? '' : $val['file'] . ',';
        }
        //删除章节附件
        if($attachments->updateFile($file_ids)) {
            return true;
        } else {
            echo getBack(1, "");
            exit;
        }
    }
    /*
     *-----------------------排序-----------------------
     *@param 类型		参数名		参数说明
     *@param char(32)	id			用于确定其在表中的位置
     *@param int(10)	sort		用于排序
     */
    public function rank($data){
        $sort = $data['sort'];
        $result = $this->saveAll($sort);
        if($result){
           return getBack(0,'1');
        }else{
           return getBack(1,'0');
        }
    }
    /*
     *判断数据序号，获取数据的父id
     *@param 类型     参数名     参数说明
     *@param string   useType      判断是序号还是父id
     *@param array   old          查询数组
     *@param string   pid          父id
     *@param string   type          数据类型
     *@param string   sort          序号
     */
    public function operationInfo($useType ,$old, $pid = '', $type = '', $sort ='') {
        foreach ($old as $v) {
            $data = $v->data;
            if ($useType == 'sort') {
                if ($data['type'] == $type) {
                    if ($data['pid'] == $pid) {
                        if ($data['sort'] == $sort) {
                            return true;
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            } else if ($useType == 'getParent') {
                if ($data['id'] = $pid) {
                    return $data['parentstr'];
                }
            }
            
        }
    }

    public function getName($data) {
        $info = $this->where('id', 'in', $data)->where('status', 0)->field('id, name')->select();
        if ($info) {
            return collection($info)->toArray();
        } else {
            return [];
        }
    }
}