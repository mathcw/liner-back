<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends TU_Controller {
    public $table = 'order_view';

    public function read_see(){
        $get = T::$U->get;
        if(empty($get['id'])){
            sys_error('缺少参数');
        }

        $data = T::$U->db->get_where('order_view',['id'=>$get['id']])->row_array();
        if(empty($data)){
            $data = [];
        }
        sys_succeed(null,$data);
    }
}