<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CruiseCompany extends TU_Controller {
    public $table = 'cruise_company';

    public function _read() {
        $get = T::$U->get;
        if (empty($get['id'])) {
            sys_error(i('MISS.PARAM'));
        }
        $data = T::$U->db->get_where($this->table,['id'=>$get['id']])->row_array();
        return $data;
    }

    public function read_modify() {
        $data = $this->_read();
        sys_succeed(null,$data);
    }
}