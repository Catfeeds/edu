<?php
namespace app\service\model;
use think\Model;
class Apps extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }
    protected $pk = 'id';

    public function getKey($id) {
        $data = $this->where('id', $id)->value('apikey');
        return $data;
    }
    public function getAll($id) {
        $data = $this->where('id', $id)->find()->data;
        return $data;
    }
}