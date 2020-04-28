<?php
defined('BASEPATH') or exit('No direct script access allowed');

function createNonceStr($length = 16)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

function httpGet($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
}

function getAccessToken()
{
    $appId = 'wx8f0ad0cf0ee7a6da';
    $appSecret = '5d7c67ef9e1d305cad82b25be2b60008';
    $access_token = T::$U->redis->get(APP_NAME . ':weixin_accessToken');

    if(empty($access_token)){

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";

        $res = json_decode(httpGet($url));
        if(!empty($res->access_token)){
            $access_token = $res->access_token;
            if ($access_token) {
                T::$U->redis->setex(APP_NAME . ':weixin_accessToken',7100,$access_token);
            }
        }else{
            if(!empty($res->errcode)){
                log_dump($res->errcode);
            }
        }

    }

    return $access_token;
}

function getJsApiTicket()
{
    $ticket = T::$U->redis->get(APP_NAME . ':weixin_jsApiTicket');

    if(empty($ticket)){
        $accessToken = getAccessToken();
        if(!empty($accessToken)){
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode(httpGet($url));
            $ticket = $res->ticket;
            if (!empty($ticket)) {
                //微信ticket 有效期7200 这里就保存个7100 让他提前失效
                T::$U->redis->setex(APP_NAME . ':weixin_jsApiTicket',7100,$ticket);
            }else{
                if(!empty($res->errcode)){
                    log_dump($res->errcode);
                }
            }
        }
    }

    return $ticket;
}

function getSignPackage($url)
{
    $appId = 'wx8f0ad0cf0ee7a6da';
    $appSecret = '5d7c67ef9e1d305cad82b25be2b60008';
    $jsapiTicket = getJsApiTicket();
    if(empty($jsapiTicket)){
        return array(
            "appId" => '',
        );
    }
    $timestamp = time();
    $nonceStr = createNonceStr();

    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
        "appId" => $appId,
        "nonceStr" => $nonceStr,
        "timestamp" => $timestamp,
        "url" => $url,
        "signature" => $signature,
        "rawString" => $string,
    );
    return $signPackage;
}
