<?php
    /*
    基于JWT的Token认证
    php 实现JWT
    PHP 7.0+
    */

    namespace driver\jwt;

    class Jwt {
        //使用HMAC生成信息摘要时所使用的密钥
        private static $key = '123456';
        // 密钥
        /* public function __construct($secret = '123456') {
            $key = $secret;
        } */
        //头部
        private static $header=array(
            'alg'=>'HS256', //生成signature的算法
            'typ'=>'JWT'  //类型
        );
 
 
        /**
        * 获取jwt token
        * @param array $payload jwt载荷  格式如下非必须
        * [
        * 'iss'=>'jwt_admin', //该JWT的签发者
        * 'iat'=>time(), //签发时间
        * 'exp'=>time()+7200, //过期时间
        * 'nbf'=>time()+60, //该时间之前不接收处理该Token
        * 'sub'=>'www.admin.com', //面向的用户
        * 'jti'=>md5(uniqid('JWT').time()) //该Token唯一标识
        * ]
        * @return bool|string
        */
        public static function getToken(array $payload) {
            if(is_array($payload)) {
                $base64header = self::base64UrlEncode(json_encode(self::$header,JSON_UNESCAPED_UNICODE));
                $base64payload = self::base64UrlEncode(json_encode($payload,JSON_UNESCAPED_UNICODE));
                $token = $base64header.'.'.$base64payload.'.'.self::signature($base64header.'.'.$base64payload,self::$key,self::$header['alg']);
                return $token;
            }else{ return false; }
        }
 
 
        /**
        * 验证token是否有效, 默认验证exp,nbf,iat时间
        * @param string $Token 需要验证的token
        * @return bool|string
        */
        public static function verifyToken(string $Token) {
            $tokens = explode('.', $Token);
            if (count($tokens) != 3)
                return false;
         
            list($base64header, $base64payload, $sign) = $tokens;
 
            //获取jwt算法
            $base64decodeheader = json_decode(self::base64UrlDecode($base64header), JSON_OBJECT_AS_ARRAY);
            if (empty($base64decodeheader['alg']))
                return false;
 
            //签名验证
            if (self::signature($base64header . '.' . $base64payload, self::$key, $base64decodeheader['alg']) !== $sign)
                return false;
        
            $payload = json_decode(self::base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);
 
            //签发时间大于当前服务器时间验证失败
            if (isset($payload['iat']) && $payload['iat'] > time())
                return false;
         
            //过期时间小宇当前服务器时间验证失败
            if (isset($payload['exp']) && $payload['exp'] < time())
                return false;
         
            //该nbf时间之前不接收处理该Token
            if (isset($payload['nbf']) && $payload['nbf'] > time())
                return false;
 
            return $payload;
        }

        /**
        * base64UrlEncode  https://jwt.io/ 中base64UrlEncode编码实现
        * @param string $input 需要编码的字符串
        * @return string
        */
        private static function base64UrlEncode(string $input) {
            return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
        }
 
        /**
        * base64UrlEncode https://jwt.io/ 中base64UrlEncode解码实现
        * @param string $input 需要解码的字符串
        * @return bool|string
        */
        private static function base64UrlDecode(string $input) {
            $remainder = strlen($input) % 4;
            if ($remainder) {
                $addlen = 4 - $remainder;
                $input .= str_repeat('=', $addlen);
            }
            return base64_decode(strtr($input, '-_', '+/'));
        }
 
        /**
        * HMACSHA256签名  https://jwt.io/ 中HMACSHA256签名实现
        * @param string $input 为base64UrlEncode(header).".".base64UrlEncode(payload)
        * @param string $key
        * @param string $alg  算法方式
        * @return mixed
        */
        private static function signature(string $input, string $key, string $alg = 'HS256') {
            $alg_config=array( 'HS256'=>'sha256' );
            return self::base64UrlEncode(hash_hmac($alg_config[$alg], $input, $key,true));
        }
    }
    
    /*
    //测试和官网是否匹配begin
    $payload=array('sub'=>'1039458625','name'=>'yangkun','iat'=>1536843835);
    $jwt=new Jwt;
    $token=$jwt->getToken($payload);
    echo "<pre>";
    echo $token;
    // eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMDM5NDU4NjI1IiwibmFtZSI6InlhbmdrdW4iLCJpYXQiOjE1MzY4NDM4MzV9._eg1enNi-aesYRvE9-UNVa9eojRhwDyztfW846K3BPU
    
    
    //对token进行验证签名
    $getPayload=$jwt->verifyToken($token);
    echo "<br><br>";
    var_dump($getPayload);
    echo "<br><br>";
    //测试和官网是否匹配end
    */
    
    /*
    //自己使用测试begin
    $payload_test=array(
        'iss'=>'admin', // (Issuer)该JWT的签发者，是否使用是可选的
        'name'=>'yangkun',
        'email'=>'kglive103945@163.com',
        'role'=>['Manager', 'admin', 'Project Administrator'],
        'iat'=>1536843835, // (Issued At)在什么时候签发的(UNIX时间)，是否使用是可选的
        'exp'=>1536847435, // (Expiration Time)什么时候过期，这里是一个Unix时间戳，是否使用是可选的
        'nbf'=>1536843835, // (Not Before)如果当前时间在nbf里的时间之前，则Token不被接受；一般都会留一些余地，比如几分钟；，是否使用是可选的
        'sub'=>'www.admin.com', // (Subject)该JWT所面向的用户，是否使用是可选的
        'jti'=>'c00d1a3116aacd5e935e0899bfc44989' // (JWT ID)为JWT提供了唯一的标识符,此声明的使用是可选的
        // 'jti'=>md5(uniqid('JWT'))
    );
    $token_test=Jwt::getToken($payload_test);
    echo "<pre>";
    echo $token_test;
    
    //对token进行验证签名
    $getPayload_test=Jwt::verifyToken($token_test);
    echo "<br><br>";
    var_dump($getPayload_test);
    echo "<br><br>";
    //自己使用时候end
    */
?>