<?php
namespace app\index\model;
use think\Model;

class Common extends Model
{
    public function getInfo($url, $param = array(), $method = 'POST', $header = array(), $multi = false) {
        $opts = array(
            CURLOPT_TIMEOUT   => 300,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER => $header
            );
        $params = $this->createParam($param);
        switch (strtoupper($method)) {
            case 'GET' :
                $opts[CURLOPT_URL] = $url . '.' . http_build_query($params);
                break;
            case 'POST' :
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = TRUE;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                return getBack(1, '不支持的请求方式！');
                break;
        }
        return $this->sendRequest($opts);
    }

    public function sendRequest($opts) {
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($error) {
            return getBack(1, '请求失败：' . $error);
        } else if ($httpCode != 200) {
            $info = json_decode($data, true);
            //调试用
            pe($data);
            return getBack(1, '数据请求失败：' . empty($data['data']) ? $data : $data['data'] );
        } else {
            return $data;
        }
    }

    public function createParam($param) {
        $text = '';
        ksort($param);
        foreach($param as $key => $val) {
            if ($key === 'app_id' || $key ==='time' || $key === 'data' ) {
                $text .= $val;
            }
        }
        $text = json_encode($text);
        $param['auth'] = createAuth($text, config('api_key'));
        return $param;
    }



}