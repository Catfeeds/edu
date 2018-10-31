<?php
namespace app\service\controller;
use app\service\model\AssignmentFiles;

class AssignmentFilesCon extends Common
{
    public function __construct() {
        parent::__construct();
    }

    /*
     * 示例
     */
    public function text () {
        $data = $this->data;
        $assFile = new AssignmentFiles();
    }
    
}