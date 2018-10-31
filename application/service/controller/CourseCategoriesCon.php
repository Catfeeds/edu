<?php
namespace app\service\controller;
use app\service\model\CourseCategories;

class CourseCategoriesCon extends Common
{
    public function __construct() {
        parent::__construct();
    }

    /*
     * 获取指定机构id与登录用户所在机构之间的所有课程类型
     * $data['organization' => '机构id', 'uid' => '登录用户id（每个$data默认都有）']
     */
    public function getCate () {
        $data = $this->data;
        $cate = new CourseCategories();
        return $cate->getInfoParam('queInfo', $data);
    }
    /*
     * 根据机构id得到该机构所有子机构及其自身的所有课程类型
     */
    public function getSonCate() {
        $data = $this->data;
        $cate = new CourseCategories();
        return $cate->getInfoParam('queAll', $data);
    }
    
}