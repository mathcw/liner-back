<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SystemApi extends TU_Controller {

    public $init_type = INIT_PUB;
    
    public function __construct() {
        if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1','::1'])){
            sys_error('没有访问权限');
        }
        set_time_limit(3600); //1小时
    }
}
