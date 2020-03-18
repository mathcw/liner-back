<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends TU_Controller {

    public $init_type = INIT_PUB;
	public function index()
	{
		echo 'welcome to api service';
	}
}
