<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserLogin extends TU_Controller {

    public $init_type = INIT_PUB;

    public function login(){

        if (!empty($_SESSION['account_id'])) {//已登陆
            $user = T::$U->db->get_where('account', array('id' => $_SESSION['account_id']))->row_array();

            $rst = array(
                'app_name'  => APP_NAME,
                'account_id'=> $_SESSION['account_id'],
                'type'      => $_SESSION['type'],
                'sid'       => $_SESSION['sid'],
                'auth_id'   => $_SESSION['auth_id'],

                'name'      => $user['name'],
            );

            $result['user'] = $rst;
            $result['success'] = true;
            echo json_encode($result);
            return;
        }

        $account = T::$U->post['account'];
        $password = md5(T::$U->post['password']);

        $user = T::$U->db->get_where('account', array('account' => $account))->row_array();
        if (empty($user)) {
            sys_error(i('ACNT_OR_PW_ERR'));
        }

        if ($user['password'] !== $password) {
            sys_error(i('ACNT_OR_PW_ERR'));
        }  
        if ($user['state'] == 0) {
            sys_error(i('ACNT_DISABLED'));
        }  

        if (empty($user['auth_id'])) {
            sys_error(i('NO_PV'));
        }  

        T::$U->load->helper('business');
        $result = login_proc($user);

        echo json_encode($result);
    }

}
