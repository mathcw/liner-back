<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dev extends TU_Controller {

    public $init_type = INIT_DEV;

    public function init_hook() {
        
        if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1','::1'])){
            sys_error('must from localhost');
        }
        set_time_limit(60*5); //5分钟
    }

    public function migrate_db(){

        T::$U->load->dbutil();
        T::$U->db->db_select(MAIN_DB);

        require_once APPPATH.'core/db/DB.php';
        tu_migrate_db();
    }

    public function refresh(){
        if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1','::1'])) {
            sys_error('must from localhost');
        }
        T::$U->redis->del(T::$U->redis->keys(APP_NAME.':*'));


        $schema = MAIN_DB;
        T::$U->load->dbutil();
        T::$U->db->db_select($schema);
        $config = @file_get_contents(APPPATH.'config/sys.json');
        
        T::$U->db->replace('sys_config',['key'=>'系统配置','value'=>$config]);

        sys_succeed('done');
    }

    private function data_init(){
        $config = @file_get_contents(APPPATH.'config/sys.json');
        
        T::$U->db->replace('sys_config',['key'=>'系统配置','value'=>$config]);
        
        //预设admin
        T::$U->db->replace('account',['id'=>1,'account'=>'admin','password'=>md5('123456')
                           ,'auth_id'=>1,'state'=>1,'name'=>'admin']);
        tu_preset_data($config);
    }

    public function system_init(){

        $schema = MAIN_DB;

        T::$U->db->query('CREATE DATABASE IF NOT EXISTS `'.$schema.'` DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;');
        T::$U->load->dbutil();
        T::$U->db->db_select($schema);

        ob_start();
        $this->migrate_db();
        ob_end_clean();

        $this->data_init();

        sys_succeed('done');
    }

}