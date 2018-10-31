<?php
namespace app\service\model;
use think\Model;

class ClassroomSections extends Model
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
     *获取file
     *@param 参数类型 参数名  参数用途
     *@param char(32) classroom_id 获取file
     */
    public function fileFind($data) {
        $res = $this->where('classroomid',$data)->where('status',0)->select();
        $num = count($res);
        $i = 0;
        while($i < $num){
            $file[$i] = $res[$i]['file'];
            $i = $i + 1;
        }
        return $file;
    }
    /**@author  李桐
     *删除（通过课堂id删除）
     *@param 参数类型 参数名 参数用途
     *@param char(32) classroom_id 获取Id，再通过Id删除信息
     */
     public function delClassroomSections($classroomId){
        $res = $this->where('classroomid',$classroomId)->where('status',0)->select();
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
    /**@author  李桐
     *获取id
     *@param 参数类型 参数名  参数用途
     *@param char(32) classroom_id 获取id
     */
    public function classroomSectionsFind($classroomId) {
        $res = $this->where('classroomid', $classroomId)->where('status',0)->select();
        $num = count($res);
        for($i = 0; $i < $num; $i++){
            $id[$i] = $res[$i]['id'];
        }
        return $id;
    }
    /**@author  李桐
     *删除（通过id删除）,并获取需要删除的file与所删除的节的id
     *@param 参数类型 参数名 参数用途
     *@param char(32)  id    通过Id删除信息
     */
    public function delIdClassroomSec($id){
        $res = $this->where('id',$id)->where('status',0)->find();
        $number = count($res);
        $j = 0;
        while($j < $number){
            //判断是章还是节
            if($res['type'] == 0){
                $list = $this->where('pid',$res['id'])->where('status',0)->select();
                $numList = count($list);
                //返回节的id和file
                for($k = 0; $k < $numList; $k++){
                    $file[$k] = $list[$k]['file'];
                    $Id[$k]   = $list[$k]['id'];
                }
            if($list != NULL){
                //有节，则删除章下所有节
                $num = count($list);
                $i = 0;
                while($i < $num){
                    $list1[$i] = (
                    ['id' => $list[$i]['id'], 'status' => '1', 'timemodified' => time()]		
                    );
                    $i = $i+1;
                }
                $this->saveAll($list1);
                }
                $this->save(['status' => '1', 'timemodified' => time()],['id' => $res['id']]);
            }
            $j = $j+1;
        }
        if(isset($file)){
            $numFile = count($file);
            $file[$numFile] = $res['file'];
            $Id[$numFile] = $res['id'];
        }else{
            $file = $res['file'];
            $Id = $res['id'];
        }
        $data['file'] = $file;
        $data['id']= $Id;
        return $data;
    }
    /**@author  李桐
     *方法详情
     *更具传入的id获取name
     *@param array    id
     */
    public function getName($id){
        $res = collection($this->where('id', 'in', $id)->where('status',0)->select())->toArray();
        return $res;
    }
}