<?php
namespace app\exam\model;
use think\Model;

class ExamClassroomProject extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }
    protected $pk = 'id';
    /**
     * 时间戳函数：自动写入创建和修改时间
     * @author 宋玉琪
     */
    protected $autoWriteTimestamp = true;
    protected $createTime = 'timecreated';
    protected $updateTime = 'timemodified';
    /**
     * 类型转换，输入时转换成时间戳，获取时转换成DATATIME
     * @author 宋玉琪
     */
    protected $type = [
        'starttime' => 'timestamp',
        'endtime' => 'timestamp',
        'timecreated' =>'timestamp',
    ];
    /**
     * 自动完成：自动设置ID
     * @author 宋玉琪
     */
    protected $insert = ['id'];
    protected function setIdAttr($val)
    {
        return $val ? $val : createGuid();
    }
    protected function getLimittimeAttr($time){
        $s = $time % 60;
        $m = intval($time / 60);
        if ($s === 0) {
            return $m . '分';
        } else {
            return $m . '分' . $s . '秒';
        }
    }
    /**
     * 带分页的万能查询
     * @author 宋玉琪
     * @param $where 查询条件
     * @param $page  页数
     * @param @rows  每页条数
     */
    public function searchByWhere($where, $page, $rows)
    {
        $res = $this->where($where)->paginate($rows, false, ['page' => $page]);
        return $res;
    }


}