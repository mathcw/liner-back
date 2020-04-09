<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Zixun extends TU_Controller {
    public $table = 'user_comment';

    public function read_see(){
        $get = T::$U->get;
        if(empty($get['id'])){
            sys_error('缺少参数');
        }
        $data = T::$U->db->get_where('user_comment',['id'=>$get['id']])->row_array();
        if(empty($data)){
            $data = [];
        }
        sys_succeed(null,$data);
    }
}