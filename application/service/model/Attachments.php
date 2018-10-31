<?php

namespace app\service\model;

use think\Model;
use app\service\validate\Valattachments;

class Attachments extends Model
{
    //课程新增id自动完成
    protected $auto = ['id'];

    protected function setIdAttr($val) {
        return $val ? $val : createGuid();
    }
    /*
     *-------------------根据id，批量查询数据-----------------
     *@param string  $str 查询的所有id的字符串   例：'id1, id2'
     *@param array() $arr 查询数据的id号与本表id一一对应的一维数组 例： ['查询id1' => '本表id1', '查询id2' => '本表id2']
     *返回查询数据id号与文件位置一一对应的一维数组 ['查询id1' => '文件位置1', '查询id2' => '文件位置2']
     */
    public function queAllFile($str, $arr) {
        $file_info = collection($this->where('id', 'in', $str)->select())->toArray();
        foreach ($arr as $key => $v) {
            foreach($file_info as $val) {
                if ($v == $val['id']) {
                    $arr[$key] = $this->createPreName() . $val['path'];
                //    $arr[$key] = $val['path'];
                    continue;
                }
            }
        }
        return $arr;
    }
    /*
     * 保存文件
     * 返回id
     */
    public function saveFile($hash, $file) {
        if ($file_info = $this->searchFile($hash)) {
            //引用次数加1
            $status = $this->where('id', $file_info['id'])->data(['timemodified' => time()])->setInc('use_number');
            if ($status) {
                return $file_info['id'];
            } else {
                echo getBack(1, '文件修改失败！');
                exit;
            }
        } else {
            //保存返回id；
            $save_info = $this->ThinkSaveFile($file);
            $data = ['path' => $save_info['path'], 'use_number' => 1, 'file_hash' => $save_info['hash'], 'timecreated' => time(), 'extendname' => $save_info['extendname'], 'mimetype' => $save_info['mimetype'], 'name' => $save_info['name'], 'filesize' => $save_info['filesize']];
            $status = $this->data($data)->allowField(true)->save();
            if ($status) {
                return $this->id;
            } else {
                echo getBack(1, '文件保存失败！');
                exit;
            }
        }
    }
    /*
     * 批量保存文件
     * @param array 需要保存的文件信息，包含文件和哈希值
     * 返回数组，包含附件id和附件哈希值的二维数组
     */
    public function saveFileBatch($files, $hashs) {
        $validate = new Valattachments();
        //获取已存在的文件信息
        $exist_hash = $this->searchFile($hashs, true);
        if($exist_hash) {
            foreach($exist_hash as $val_h) {
                array_walk($files, function(&$val, $key, $val_h){
                    if ($val_h['file_hash'] === $val['hash']) {
                        $val['file_id'] = $val_h['id'];
                    }
                }, $val_h);
            }
        }
        $return_data = array();
        foreach($files as $val_f) {
            if(empty($val_f['file_id'])) {
                if (!$validate->check(['files' => $val_f['file']])) {
                    echo getBack(1, $validate->getError());
                    exit;
                } else {
                    $save_info = $this->ThinkSaveFile($val_f['file']);
                    $data = ['path' => $save_info['path'], 'file_hash' => $save_info['hash'], 'use_number' => 1, 'timecreated' => time(), 'mimetype' => $save_info['mimetype'], 'extendname' => $save_info['extendname'], 'name' => $save_info['name'], 'filesize' => $save_info['filesize']];
                    $status = $this->data($data, true)->isUpdate(false)->allowField(true)->save();
                    if ($status) {
                        $return_data[$save_info['hash']] = $this->id;
                    } else {
                        echo getBack(1, '文件保存失败！');
                        exit;
                    }
                }
            } else {
                //该文件已存在，引用次数加1
                $up_status = $this->where('id', $val_f['file_id'])->data(['timemodified' => time()])->setInc('use_number');
                if ($up_status) {
                    $return_data[$val_f['hash']] = $val_f['file_id'];
                } else {
                    echo getBack(1, '文件保存失败！');
                    exit;
                }
            }
        }
        return $return_data;
    }

    /*
     * 修改单个或多个文件,引用次数减一
     * @param string $str 文件id,多个文件的id以逗号分隔或者为自然数索引数组
     */
    public function updateFile($str) {
        if (empty($str)) {
            return true;
        }
        $status = $this->where('id', 'in', $str)->data(['timemodified' => time()])->setDec('use_number');
        if (!$status) {
            echo getBack(1, '内部错误，请联系相关人员！');
            exit;
        } else {
            return true;
        }
    }
    /*
     * 修改单个或多个文件,引用次数加一
     * @param string $str 文件id,多个文件的id以逗号分隔或者为自然数索引数组
     */
    public function addFile($str) {
        if (empty($str)) {
            return true;
        }
        $status = $this->where('id', 'in', $str)->data(['timemodified' => time()])->setInc('use_number');
        if (!$status) {
            echo getBack(1, '内部错误，请联系相关人员！');
            exit;
        } else {
            return true;
        }
    }
    /*
     * 根据文件id得到文件
     * @param string $id 文件id
     */
    public function getFilePic($id, $width = false, $height = false) {
        //判断该文件是否有效
        $info = $this->where('id', $id)->find()->toArray();
        $real_path = dirname(APP_PATH) . DS . $info['path'];
        $return_path = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        if (!is_file($real_path)) {
            echo getBack(1, '暂无该文件！');
            exit;
        }
        if ($width || $height) {
            $name_arr = explode('.', $info['name']);
            if (!$width) {
                $width = config('picture_width');
            }
            if (!$height) {
                $height = config('picture_height');
            }
            $name = $name_arr[0] . '_' . $width . '_' . $height;
            $thumbnail = dirname(APP_PATH) . DS . str_replace($name_arr[0], $name, $info['path']);
            if (!is_file($thumbnail)) {
                $image = \think\Image::open($real_path);
                $image->thumb($width, $height)->save($thumbnail);
            }
            return $return_path . str_replace($name_arr[0], $name, $info['path']);
        }
        return $return_path . $info['path'];
    }
    /*
     * 根据文件id下载文件
     * @param string $id 文件id
     */
    public function downloadFile($id) {
        //判断该文件是否有效
        $info = $this->where('id', $id)->find()->toArray();
        $real_path = dirname(APP_PATH) . DS . $info['path'];
        if (!is_file($real_path)) {
            echo getBack(1, '暂无该文件！');
            exit;
        }
        $return_path = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_ADDR'] .  dirname($_SERVER['SCRIPT_NAME']) . DS . $info['path'];
        return $return_path;
       /* $name = '13';
        $types = 'jpg';
        $content_type = "image/jpg";
        $type = 'attachment';//下载
        $file_path = 'D:\wamp64\www\edu\public\uploads\20171204\2a\6b\f7\b8\2a6bf7b8ddcfa2062a79ac9f6aeea2c0.jpg';
        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);
        //下载文件需要用到的头
        //   Header("Content-type: application/octet-stream");
       Header("Content-type: " . $content_type);//代表文件MIME类型是文件流格式
       Header("Accept-Ranges: bytes");//按照字节格式返回
       Header("Accept-Length:".$file_size);//返回文件大小
    //   Header("Content-Disposition: attachment; filename=".$name . '.'.$types);
       Header("Content-Disposition: " . $type . "; filename=".$name . '.'.$types);//告诉浏览器，文件是可以当做附件被下载,弹出客户端对话框，对应的文件名
        $buffer=1024;
        $file_count=0;
        //向浏览器返回数据
        //防止服务器瞬间压力增大，分段读取
        while(!feof($fp) && $file_count < $file_size){
            $file_con = fread($fp,$buffer);
            $file_count += $buffer;
            echo $file_con;
        }
        fclose($fp);
        exit;*/

    }



    /*
     * 根据hash值查文件
     * @param string hash 查询文件的哈希值
     * @param bool type 判断是否为子查询
     */
    public function searchFile($hash, $type = false) {
        if ($type) {
            $file_info =collection($this->where('file_hash', 'in', $hash)->select())->toArray();
        } else {
            $file_info = $this->where(['file_hash' => $hash])->find();
        }
        if ($file_info) {
            return $file_info;
        } else {
            return false;
        }
    }
    /*
     * ThinkPHP内部保存图片
     */
    public function ThinkSaveFile($file) {
        $pre = 'public'.DS .'uploads' . DS;
        $info = $file->rule(function (){
            $pic_name = md5(microtime(true));
            $a = substr($pic_name, 0, 2);
            $b = substr($pic_name, 2, 2);
            $c = substr($pic_name, 4, 2);
            $d = substr($pic_name, 6, 2);
            return $savename = date('Ymd') . DS . $a . DS . $b . DS . $c . DS . $d . DS . $pic_name;
        })->move(ROOT_PATH . $pre);
        if ($info) {
            return ['path' => $pre . $info->getSaveName(), 'hash' => $info->md5(), 'extendname' => $info->getExtension(), 'mimetype' => $info->getInfo('type'), 'name' => $info->getFilename(), 'filesize' => $info->getSize()];
        } else {
            echo getBack(1, '文件保存失败！');
            exit;
        }
    }
    /*
     * 创建文件前缀
     * 返回文件绝对路径除保存路径的所有
     */
    public function createPreName() {
        $protocol = empty($_SERVER['HTTP_X_CLIENT_PROTO']) ? 'http://' : $_SERVER['HTTP_X_CLIENT_PROTO'] . '://';
        $baseUrl = str_replace('\\', DS, dirname($_SERVER['SCRIPT_NAME']));
        return $protocol . $_SERVER['HTTP_HOST'] . $baseUrl . DS;
    }

    /**@author 叶文
     * @param $file_path 文件路径
     * @return int 0 文件不存在
     */
    public function downFile($file_path) {
        $file_path = iconv('utf-8','gb2312', $file_path);
        if ($this->my_file_exists($file_path) == false) {
            return 0;
        }else {
            $file_name = basename($file_path);
            $file = file_get_contents($file_path);
            $file_size = strlen($file);
            $fp = fopen($file_path,'r');
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename={$file_name}");
            $buffer = 1024;
            $file_count = 0;
            while(!feof($fp) && ($file_size-$file_count>0)) {
                $file_data = fread($fp,$buffer);
                $file_count += $buffer;
                echo $file_data;
            }
            fclose($fp);
        }
    }
    /**@author 叶文
     * 判断远程或者本地文件是否存在
     * @param $file 文件地址
     * @return bool true 文件存在 false 文件不存在
     */
    function my_file_exists($file) {
        if(preg_match('/^http:\/\//',$file) || preg_match('/^https:\/\//',$file)) {
            //远程文件
            if(ini_get('allow_url_fopen')){
                if(@fopen($file,'r')) return true;
            }
            else{
                $parseurl=parse_url($file);
                $host=$parseurl['host'];
                $path=$parseurl['path'];
                $fp=fsockopen($host,80, $errno, $errstr, 10);
                if(!$fp)return false;
                if ($host=='http') {
                    fputs($fp,"GET {$path} HTTP/1.1 \r\nhost:{$host}\r\n\r\n");
                    if(preg_match('/HTTP\/1.1 200/',fgets($fp,1024))) return true;
                }else{
                    fputs($fp,"GET {$path} HTTPS/1.1 \r\nhost:{$host}\r\n\r\n");
                    if(preg_match('/HTTPS\/1.1 200/',fgets($fp,1024))) return true;
                }
            }
            return false;
        }
        // 本地文件
        return file_exists($file);
    }
    public function delfiles($ids) {

    }

    //判断文件是否存在
    public function checkFileVlid($path) {
        if(!file_exists($path)){
            pe('58');
             echo getBack(1, '暂无该文件信息！');
             exit;
        }
        pe('0000');
    }
}