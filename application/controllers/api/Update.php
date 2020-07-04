<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update extends TU_Controller {

    public $init_type = INIT_PUB;

    public function front(){
        if(empty($_FILES) || empty($_FILES['file'])){
            echo 'empty update package';
            return;
        }
        $work_dir = 'upgrade';
        $target = 'py-front';
        if(!is_dir($work_dir)){
            mkdir($work_dir, 0755, TRUE);
        }
        if(!is_dir('../'.$target)){
            mkdir('../'.$target, 0755, TRUE);
        }
        $save_path = $work_dir . '/dist.zip';
        move_uploaded_file($_FILES['file']['tmp_name'], $save_path);
        unzip($save_path,$work_dir);
        $unzip_path = $work_dir.'/dist';
        if(is_dir($unzip_path)){
            copy_dir($unzip_path, '../'.$target);
        }
        echo 'done';
    }
}