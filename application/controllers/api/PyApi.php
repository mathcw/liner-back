<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PyApi extends TU_Controller {

    public $init_type = INIT_PUB;

    public function gen(){
        $post = T::$U->post;
        if(empty($post['cond'])){
            sys_succeed(null,['rst'=>'']);
            return ;
        }
        $cmd = 'python3 '.APPPATH.'/controllers/api/bjadks.py '.$post['cond'].' '.APPPATH.'/controllers/api/';
        
        $cmd = compati_path($cmd);
        exec($cmd,$screen,$ret);
        if(!empty($screen)){
            $str = '';
            if(is_array($screen)){
                foreach ($screen as $value) {
                    $str .=  $value .PHP_EOL;
                }
            }else{
                $str = $screen;
            }
            file_put_contents(tu_file_path('py/output.txt'),$str);
            $url = base_url(tu_file_path('py/output.txt'));
            sys_succeed(null,['rst'=>$url]);
            return ;
        }else{
            sys_succeed(null,['rst'=>'']);
            return ;

        }
    }
}