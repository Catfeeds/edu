<?php
namespace app\service\controller;
use app\service\model\User;
use app\service\model\Course;
use app\service\model\CourseCategories;
use app\service\model\Assignment;
use app\service\model\Profession;

class Index extends Common
{
    public function __construct(){
        parent::__construct();
    }


    /**
     * // 具体方法实现
     * @return [type] [description] 返回数据通过getBack(code, data)方法返回,code：1：错误返回；0：正确返回。data返回的数据信息
     */
    public function index() {
        //用户传递的处理数据
        $data = $this->data;
        //返回调用方法，code返回代码(0:成功，1:失败)，$mes返回消息或数据
        //return getBack(code, $mes);

    }
    /*
     * 登录
     */
    public function checklogin() {
        $user = new User($this->data);
        $userInfo = $user->login();
        return $userInfo;
    }

}
