<?php
function p($param){
    header("Content-type:text/html;charset=utf-8");
    print_r($param);
    echo '<br />';
}
function pe($param){
    header("Content-type:text/html;charset=utf-8");
    print_r($param);
    echo '<br />';
    exit;
}
function getrealip(){
    if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]){
        $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
    }elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]){
        $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
    }elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]){
        $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
    } elseif (getenv("HTTP_X_FORWARDED_FOR")){
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } elseif (getenv("HTTP_CLIENT_IP")){
        $ip = getenv("HTTP_CLIENT_IP");
    } elseif (getenv("REMOTE_ADDR")){
        $ip = getenv("REMOTE_ADDR");
    }else{
        $ip = "Unknown";
    }
}

function createGuid($hyphen = '') {
    mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $uuid = substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid,12, 4) . $hyphen
            . substr($charid,16, 4) . $hyphen
            . substr($charid,20,12);
    return $uuid;
}
/**
 * 加密数据
 * @param  string $data 加密数据
 * @param  string $key  加密key
 * @return [type]       [description]
 */
function createAuth($data = '', $key = 'encrypt') {
    $encrypt = md5(md5($data) . $key);
    return $encrypt;
}
/**
 * 数据返回方法
 * @param  [type]  $code     执行代码
 * @param  string  $data     返回数据
 * @param  tab $original     标记
 * @return [type]            [description]
 */
function getBack($code, $data = '', $tab = '') {
    $reData['status'] = $code;//1:错误;0:正确
    $reData['data'] = $data;
    if ($tab) {
        $reData['tab'] = $tab;
    }
    return json_encode($reData);
}
/**
 * 返回数据解析
 * @param  string $json_data 返回的json数据
 * @return [type]            [description]
 */
function codeManage($json_data = '') {
    $data = json_decode($json_data, true);
   if($data['status'] == 'fail') {
        $message = '';
        if (is_array($data['data'])) {
            foreach ($data['data'] as $key => $val) {
                $message .= config('code_meaasge.' . $val) . ((count($data['data']) -1) == $key ? '' : ',');
            }
        } else {
            $message = config('code_meaasge.' . $data['data']);
        }
        $data['data'] = $message;
        return $data;
   } else if ($data['status'] == 'success') {
        return $data;
   } else if ($data['status'] == 'original') {
        $data['status'] = 'fail';
        return $data;
   } else if (empty($data)) {
        $data['status'] = 'fail';
        $data['data'] = '无效的请求参数！';
        return $data;
   }
}
/**
 * 无限极分类
 * @param  array $data 传入需要分类的数组数据
 * @param  string $id 主键key值
 * @param  string $data_pid 父id的key值
 * @param  string $pid 指定父id值
 * @param  bool $is_hierarchy 是否分层级，还是返回一维id数组，默认返回id数组
 * @return  array 返回数组数据
 */
function  getClassify($data, $id, $data_pid, $pid, $is_hierarchy = false, &$array = null) {
    foreach ($data as $k => $v) {
        if ( (string)$pid == (string)$v[$data_pid] ) {
            if ($is_hierarchy) {
                $array[config('classify_name')][$v[$id]] = $v;
                getClassify($data, $id, $data_pid, $v[$id], $is_hierarchy, $array[config('classify_name')][$v[$id]]);
            } else {
                $array[] = $v[$id];
                getClassify($data, $id, $data_pid, $v[$id], $is_hierarchy, $array);
            }
        }
    }
    return isset($array) ? $array : false;
}
function  getClassify_index($data, $id, $data_pid, $pid) {
    $tree = false;
    foreach ($data as $k => $v) {
        if ( (string)$pid == (string)$v[$data_pid] ) {
            $temp = getClassify_index($data, $id, $data_pid, $v[$id]);
            if ($temp) {
                $v[config('classify_name')] = $temp;
                $tree[] = $v;
            } else {
                $tree[] = $v;
            }
        }
    }
    return $tree;
}


