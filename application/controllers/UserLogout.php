<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserLogout extends TU_Controller {

    public function logout() { 
        T::$U->db->delete('sys_session', array('sid' => $_SESSION['sid']));  
        destory_session();
        $_SESSION  = [];
        sys_error(-1);
    }
}
