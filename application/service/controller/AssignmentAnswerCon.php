<?php
namespace app\service\controller;
use app\service\model\AssignmentAnswer;

class AssignmentAnswerCon extends Common
{
    public function __construct() {
        parent::__construct();
    }

    /*
     * 示例
     */
    public function text () {
        $data = $this->data;
        $assAns = new AssignmentAnswer();
    }
    /*author 李桐
     * 更新答案
     */
    public function upanswer () {
        $data = $this->data;
        $assAns = new AssignmentAnswer();
        $result = $assAns->upanswer($data);
        return $result;
    }
    
}