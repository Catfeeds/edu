<?php
namespace app\service\controller;

use app\service\model\Attachments;

class AttachmentsCon extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /**@author 叶文
     * 下载文件
     */
    public function downFile() {
        $data = $this->data;
        $file_path = $data['filepath'];
        $attachments = new Attachments;
        $res = $attachments->downFile($file_path);
        if ($res === 0) {
            return getBack(1,'文件不存在');
        }else {
            return getBack(0,'succeed');
        }
    }
    /*
     * 文件下载
        module：service
        controller：Attachments_Con
        action： getFile
        位置：id
        是否缩略图：是|否
        缩小比例：长，宽
        是否裁剪：是|否
        裁剪位置：裁剪长开始位置，裁剪宽开始位置，裁剪长度，裁剪宽度
     */
    public function getFile() {
        $data = $this->data;
        $id = false;
        if (isset($data['id']) && isset($data['type'])) {
            $id = $data['id'];
        } else {
            return getBack(1, '参数错误！');
        }
        $Attachments = model('Attachments');
        $result = '';
        if ($data['type'] == 'getpic') {
            $width = isset($data['width']) ? ($data['width'] > config('picture_width') ? config('picture_width') : $data['width']) : false;
            $height = isset($data['height']) ? ($data['height'] > config('picture_height') ? config('picture_height') : $data['height']) : false;
            $result = $Attachments->getFilePic($id, $width, $height);
        } else if ($data['type'] == 'downgile') {
            $result = $Attachments->downloadFile($id);
        }
        return getBack(0, $result);
    }


}