<?php

namespace app\service\validate;

use think\Validate;

class Valattachments extends Validate
{
    protected $rule = [];
    public function __construct() {
        parent::__construct();
        $max =  50 * 1024 * 1024;//最大50M
        $this->rule['files'] = 'file|fileSize:' . $max . '';
    }
    protected  $message = [
        'files.fileSize' => '上传文件过大'
    ];
}
