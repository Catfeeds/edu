<?php
namespace app\service\model;
use think\Model;

class CoursSections extends Model
{
    protected $pk = 'id';
    protected $auto = ['id'];

    protected function setIdAttr() {
        return createGuid();
    }

}