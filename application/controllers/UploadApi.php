<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadApi extends TU_Controller {

    public $init_type = INIT_USER;

    public function __construct()
    {
        T::$C = & $this;
        T::$U = new CI_Controller();
        T::$H = new stdClass();

        T::$U->load->helper(['init','common','combo','util','url','business']);
        T::$U->header =  getHeader();
        recover_session();
        if(empty($_SESSION[$this->uid_key])){
            sys_error(-1);
        }
        
        $this->init_hook();
    } 
    
    public function upload($type) {
        switch ($type) {
            case 'cruiseVideo':
                $save_path=sys_upload('file','all',VIDEO_LIMIT, '', $type.'/');
                break;
            default:
                $save_path=sys_upload('file','all',PDF_LIMIT, '', $type.'/');
                break;
        }
        //
        $save_path  = get_server_path() . '/liner-back/'.$save_path;

        $rst = array('save_path'=>$save_path);
        sys_succeed(i('UPLOAD.SUC'),null,$rst); 
    }
}
