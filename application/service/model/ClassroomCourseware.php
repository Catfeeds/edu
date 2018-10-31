<?php
namespace app\service\model;
use think\Model;

class ClassroomCourseware extends Model
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
    /**@author  李桐
     *方法详情
     *删除（通过classroomsections删除classroomcourseware）
     *@param 参数类型 参数名  参数用途 
     *@param char(32) classroom_sections  定位需要删除的信息
     */
    public function delClassroomCourseware($classroom_sections) {
        $number = count($classroom_sections);
        $k = 0;
        for($j = 0; $j < $number; $j++){
            $temp = $this->where('classroom_sections',$classroom_sections[$j])->where('status',0)->find();
            if(isset($temp)){
                $res[$k] = $temp;
                $k = $k+1;
            }
        }
        $num = count($res);
        $i = 0;
        while($i < $num){
            $list[$i] = (
                        ['id' => $res[$i]['id'], 'status' => '1', 'timemodified' => time()]		
                    );
            $i = $i+1;
        }
        $this->saveAll($list);
    }


}