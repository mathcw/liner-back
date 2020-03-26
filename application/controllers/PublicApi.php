<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PublicApi extends TU_Controller {

    public function init(){
        $post = T::$U->post;
        if(isset($post['lang'])){
            set_lang($post['lang']);
            $_SESSION['lang'] = $post['lang'];
        }
        $init = tu_entry_data();

        $user = T::$U->db->get_where('account', array('id' => $_SESSION['account_id']))->row_array();

        $init['user'] =  array(
            'app_name'  => APP_NAME,
            'account_id'=> $_SESSION['account_id'],
            'sid'       => $_SESSION['sid'],
            'auth_id'   => $_SESSION['auth_id'],
            'name'      => $user['name'],
        );

        sys_succeed(null,$init);
    }
}
