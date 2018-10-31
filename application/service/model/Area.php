<?php
namespace app\service\model;
use think\Model;

class Area extends Model
{
    protected function initialize()
    {
        parent::initialize();
    }
    protected $pk = 'id';
    protected $parenstr_prefix = '0,';//该数据表最高级别parenstr值
    protected $pid_prefix = '0';//该数据表最高级别pid值

    

}