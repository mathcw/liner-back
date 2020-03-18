<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function login_proc($user){

    reset_session($user['id']%SHARED_SIZE);

    $new_session = array(
        'sid'       => $_SESSION['sid'],
        'account_id'=> $user['id'],
        'ip' => T::$U->input->ip_address(),
        'expire_time'=> time() + SESSION_TTL,
        'ios_tkn'=> ''
    );

    if(!empty(T::$U->post['ios_tkn'])){
        $new_session['ios_tkn'] = T::$U->post['ios_tkn'];
    }

    $old_session = T::$U->db->get_where('sys_session',array('account_id'=>$user['id']))->row_array();
    if($old_session){
        T::$U->redis->del($old_session['sid']);
        T::$U->db->delete('sys_session', array('account_id' => $user['id']));
    }

    $session_exist = T::$U->db->get_where('sys_session', array('sid' => $_SESSION['sid']))->row_array();
    if ($session_exist) {
        T::$U->db->update('sys_session', $new_session, array('sid' => $_SESSION['sid']));
    } else {
        T::$U->db->insert('sys_session', $new_session);
    }

    $s = [
        'app_name'   => APP_NAME,
        'account_id' => $user['id'],
        'auth_id'    => $user['auth_id'],
        'sid'        => $_SESSION['sid'],
        'account'    => $user['account'],
        'name'       => $user['name'],

        'front_enum' =>T::$U->redis->get(APP_NAME.':EnumVer'),
    ];


    if(!empty(T::$U->post['ios_tkn'])){
        $s['ios_tkn'] = T::$U->post['ios_tkn'];
    }

    $_SESSION = $s;

    T::$U->redis->setex($_SESSION['sid'],SESSION_TTL,json_encode_unescaped($_SESSION));

    T::$U->load->library('user_agent');
    $log = array(
        'account_id'=>$_SESSION['account_id'],
        'ip'=>T::$U->input->ip_address(),
        'user_agent'=>T::$U->agent->agent_string(),
        'is_app'=> empty(T::$U->get['app'])?0:1,
    );
    T::$U->db->insert('sys_login_log',$log);

    $user = array(
        'app_name' => APP_NAME,
        'account_id'=> $_SESSION['account_id'],
        'sid'       => $_SESSION['sid'],
        'auth_id'   => $_SESSION['auth_id'],

        'name'      => $user['name'],
    );

    $result['user'] = $user;
    $result['success'] = true;
    return $result;
}
