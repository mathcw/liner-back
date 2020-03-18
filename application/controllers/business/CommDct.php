<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CommDct extends TU_Controller {
    public $table = 'comm_dict';
    
    public function submit(){
        sys_submit(T::$U->post, $this->table, $this->id_field);
        sys_succeed(i('SAVE.SUC'));
        tu_update_enum(true);
    }

    public function destroy() {
        sys_destroy(T::$U->post, $this->table, $this->id_field);
        sys_succeed(i('DEL.SUC'));
        tu_update_enum(true);
    }
}