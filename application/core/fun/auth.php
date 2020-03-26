<?php
defined('BASEPATH') OR exit('No direct script access allowed');


function tu_authority_check() {
    $pass = ['Session.*','PublicApi.*','UserLogout.*','UploadApi.*'];

    $class = T::$U->router->fetch_class();
    $method = T::$U->router->fetch_method();
    if(in_array($class.'.'.$method,$pass) || in_array($class.'.*',$pass)){
        //pass
    }else if(in_array(tu_get_request(),T::$H->pem_urls)){
        //pass
    }else{
        sys_error(i('NO_PV'));
    }
}

function tu_destroy_session($account_id)
{
    $q = T::$U->db->get_where('sys_session',['account_id'=>$account_id])->row_array();
    if(!empty($q)){
        T::$U->db->delete('sys_session', ['sid' => $q['sid']]);  
        T::$U->redis->del([$q['sid']]);
    }
    $q = T::$U->db->get_where('sys_session_app',['account_id'=>$account_id])->row_array();
    if(!empty($q)){
        T::$U->db->delete('sys_session_app', ['sid' => $q['sid']]);  
        T::$U->redis->del([$q['sid']]);
    }
}

function tu_access_log()
{
    if(empty($_SESSION['account_id'])){
        return;
    }
    tu_submit_urls();
    $url = tu_get_request();
    if(empty(T::$H->submit_urls[$url])){
        return;
    }
    $log = array(
        'account_id'=>$_SESSION['account_id'],
        'ip'=>T::$U->input->ip_address(),
        'path_info'=>$_SERVER['REQUEST_URI'],
        'get_param'=>json_encode_unescaped(T::$U->get),
        'post_param'=>json_encode_unescaped(T::$U->post),
    );
    T::$U->db->insert('sys_access_log',$log);
}

function tu_preset_data($sys_str)
{
    $obj = json_decode($sys_str,true);
    if(empty($obj['preset_data'])){
        return;
    }
    foreach ($obj['preset_data'] as $table => $rows) {
        foreach ($rows as $row) {
            foreach ($row as $k => $v) {
                if(is_array($v)){
                    $row[$k] = json_encode_unescaped($v);
                }
            }
            T::$U->db->replace($table,$row);
            if($table == 'auth'){
                $auth_id = $row['id'];
                T::$U->redis->del([
                    APP_NAME.':pem_urls:'.$auth_id,
                    APP_NAME.':pem_paths:'.$auth_id,
                    APP_NAME.':pem_paths:'.$auth_id
                ]);
            }
        }
    }
}