<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends TU_Controller {

    public $table = 'sys_login_log';
    public $view = 'sys_login_log_view';
    public $order_field = 'id';
    public $order_dir = 'asc';
}
