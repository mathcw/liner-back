<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// http header 
function getHeader(){

    $headers = []; 
    
    if(!empty($_SERVER['SERVER_SOFTWARE'])){
        // Apache 
        if(strpos($_SERVER['SERVER_SOFTWARE'],'Apache') !== FALSE){
            $q = getallheaders();
            $headers['AUTHORIZATION'] = $q['Authorization'] ??'';
        }
        // nginx
        if(strpos($_SERVER['SERVER_SOFTWARE'],'nginx') !== FALSE){
            $headers['AUTHORIZATION'] = $_SERVER['HTTP_AUTHORIZATION']??'';
        }
    }

	return $headers;
}

function init_req(){
    T::$U->header =  getHeader();
    
    //获取post，get参数
    $raw = file_get_contents("php://input");

    $input = json_decode($raw,true);

    if(is_array($input)) {

        T::$U->post = &$input;

    } else {

        T::$U->post = $_POST??[];
    }

    T::$U->get = $_GET??[];

}

function reset_session($uid=null)
{
    $svc = T::$C->svc;
    if($uid){
        init_svc($uid);
    }else{
        init_svc_gp('default');
    }
    

    list($usec, $sec) = explode(" ", microtime());
    $rand = explode('.',$usec)[1] + rand(100000,999999);

    $_SESSION['sid'] = $uid.':'.date('YmdHis').$rand;
}

function recover_session()
{
    
    if(empty(T::$U->header['AUTHORIZATION'])){
        sys_error(-1);
    }

    $uid = explode(':',T::$U->header['AUTHORIZATION'])[0];

    if($uid){
        init_svc($uid);
    }else{
        init_svc_gp('default');
    }

    $s = T::$U->redis->get(T::$U->header['AUTHORIZATION']);
    

    if(!$s){
        sys_error(-1);
    }

    $_SESSION = json_decode($s,true);
}

function destory_session(){
    T::$U->redis->del([$_SESSION['sid']]);
}

function init_svc($id)
{
    $svc = T::$C->svc;
    T::$U->redis = & $svc::redis($id);
    T::$U->db = & $svc::db($id);
}

function init_svc_gp($gp)
{
    $svc = T::$C->svc;
    T::$U->redis = & $svc::redis_gp($gp);
    T::$U->db = & $svc::db_gp($gp);
}

function set_lang($lang){
    $map = [
        'zh-CN'=>'ZhCN',
        'zh-TW'=>'ZhTW',
        'en-US'=>'English',
        'pt-BR'=>'Portug',
    ];
    $lang_kind = $map[$lang];
    if(!empty($lang)){
        I18n::$c = & $lang_kind ::$c;
        I18n::$lang = $lang;
    }
}
