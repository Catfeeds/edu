<?php
namespace app\service\controller;

class Area extends Common
{
    public function __construct(){
        parent::__construct();
    }
    //获取下级所有行政区域
    public function getNextArea() {
        $data = $this->data;
        $code = '0';
        if (@!empty($data['code'])) {
            $code = $data['code'];
        }
        $area = model('Area');
        $thisId = $area->where(['code' => $code, 'status' => 1])->value('id');
        if (!$thisId) {
            return getBack(1, '当前选择无效！');
        }
        $allSon = collection($area->where(['status' => 1, 'pid' => $thisId])->field('code, name')->select())->toArray();
        if ($allSon) {
            return getBack(0, $allSon);
        } else {
            return getBack(1, '没有下级了！')
        }
    }

    //获取某一行政区域的所有上级行政区域代码
    public function getPath() {
        $data = $this->data;
        if (@!empty($data['code'])) {
            return getBack(1, '参数错误！');
        }
        $area = model('Area');
        $parentstr = $area->where(['status' => 1, 'code' => $data['code']])->value('parentstr');
        if (!$parentstr) {
            return getBack(1, '当前选择无效！');
        }
        $parentstr = mb_substr($parentstr, mb_strlen($area->parenstr_prefix)) . $data['code'];
        $arr = explode(',', $parentstr);
        return getBack(0, $arr);
    }

}
