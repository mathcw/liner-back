<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends TU_Controller {
    public $table = 'account';


    public function reset_password(){
        $post = T::$U->post;
        if(empty($post['id'])){
            sys_error(i('MISS.PARAM'));
        }
        $password = md5('123456');

        T::$U->db->trans_start();

        T::$U->db->update('account',['password'=>$password],['id'=>$post['id']]);

        T::$U->db->trans_complete();
		sys_succeed(i('SAVE.SUC'));
    }

    public function set_password(){
        $post = T::$U->post;
        if(empty($post['id'])){
            sys_error(i('MISS.PARAM'));
        }
        if(empty($post['password'])){
            sys_error(i('MISS.PARAM'));
        }

        $password = md5($post['password']);

        T::$U->db->trans_start();

        T::$U->db->update('account',['password'=>$password],['id'=>$post['id']]);

        T::$U->db->trans_complete();
		sys_succeed(i('SAVE.SUC'));
    }

    public function read_auth(){
        $get = T::$U->get;
        if(empty($get['id'])){
            sys_error(i('MISS.PARAM'));
        }

        $q = T::$U->db->get_where($this->table,['id'=>$get['id']])->row_array();

        sys_succeed(null,$q);
    }  

    public function set_auth(){
        $post = T::$U->post;
        if(empty($post['id']) || empty($post['auth_id'])){
            sys_error(i('MISS.PARAM'));
        }
        T::$U->db->trans_start();
        T::$U->db->update('account',['auth_id'=>$post['auth_id']],['id'=>$post['id']]);
        T::$U->db->trans_complete();
        sys_succeed(i('EXEC.SUC'));
    }
}