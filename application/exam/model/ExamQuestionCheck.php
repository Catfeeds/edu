<?php
namespace app\exam\model;
use think\Model;

class ExamQuestionCheck extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }
    protected $pk = 'id';
    protected $auto = ['id'];
    protected function setIdAttr($id) {
        return $id ? $id : createGuid();
    }

    /**
     * 加入问题审核信息
     * @author 宋玉琪 
     * @param $data  前端总数据
     */
    public function addcheckmsg($data)
    {
        $data = [
            'questionid' => $data['id'],
            'text' => isset($data['msg']) ? $data['msg'] : '',
            'timecreated' => time()
        ];
        $res = $this->save($data);
        return $res;
    }
    /**
     * 审核未通过的原因
     * @author 宋玉琪 
     * @param $id  问题ID
     */
    public function showCheckMsg($id)
    {
        $info = $this->where('questionid', $id)->find();
        return $info;
    }
}