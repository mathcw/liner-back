<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'fun/init.php';
require_once 'fun/auth.php';
require_once 'fun/read.php';
require_once 'fun/write.php';
require_once 'fun/x.php';
require_once 'i18n.php';
require_once 'empty.php';
require_once 'shard/Shard.php';


/**
 * @property TU_Controller $C
 * @property U $U
 */
class T {
    public static $U;//CI核心
    public static $H;//数据中心，全局数据均可保存到这里
    public static $C;//url调用的class对象
    public static $ROLLBACKTASK = []; // 回滚任务
    public static $BASEDATESYNCTASK = []; //基础数据同步任务
    public static $TASK = [];//异步任务
}

/**
 * @property CI_DB_forge $dbforge
 * @property CI_DB_query_builder $db
 * @property CI_Config $config
 * @property CI_Exceptions $exceptions
 * @property CI_Hooks $hooks
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_Log $log
 * @property CI_Output $output
 * @property CI_Router $router
 * @property CI_Session $session
 */
class U{

}

class TU_Controller {

    public $id_field = 'id';
    public $order_field = 'last_update';
    public $order_dir = 'desc';

    public $init_type = INIT_USER;

    public $svc = MAIN_SVC;

    public $uid_key = MAIN_UID_KEY;

    public function __construct()
    {
        T::$C = & $this;
        T::$U = new CI_Controller();
        T::$H = new stdClass();


        T::$U->load->helper(['init','common','combo','util','url','business','weixin']);
        init_req();

        switch ($this->init_type) {
            case INIT_DEV:
                init_svc_gp('dev');
                break;
            case INIT_PUB:
                init_svc_gp('default');
                break;
            case INIT_SESSION:
                recover_session();
                break;
            case INIT_USER:
                recover_session();
                if(empty($_SESSION[$this->uid_key])){
                    sys_error(-1);
                }
                break;
            default:
                sys_error('wrong init type');
                break;
        }
        
        $this->init_hook();
    }

    public function init_hook()
    {
        T::$H->APP_NAME = MAIN_DB;
        T::$H->FILEPATH = 'files/'.T::$H->APP_NAME.'/';

        if($this->init_type == INIT_PUB){
            tu_pub_init();
        }
        if($this->init_type == INIT_SESSION){
            tu_pub_init();
        }
        if($this->init_type == INIT_USER){
            tu_user_init();
            tu_authority_check();
        }
    }

    public function read() {
        list($total, $items) = sys_read(
                empty($this->view) ? $this->table : $this->view, 
                T::$U->get, 
                $this->order_field,
                $this->order_dir
        );
        if(method_exists($this, 'read_convert')){
            $this->read_convert($items);
        }
        sys_succeed(NULL, $items, array('total' => $total));
    }
    
    public function submit() {
        sys_submit(T::$U->post, $this->table, $this->id_field);
        sys_succeed(i('SAVE.SUC'));
    }

    public function create_batch() {
        sys_create_batch(T::$U->post, $this->table, $this->id_field);
        sys_succeed(i('SAVE.SUC'));
    }

    public function toggle($field){
        $post = T::$U->post;
        if(empty($post['id']) || !isset($post[$field])){
            sys_error(i('MISS.PARAM'));
        }
        $set = 1;
        if($post[$field]>0){
            $set = 0;
        }
        T::$U->db->update($this->table,[$field=>$set],['id'=>$post['id']]);
        sys_succeed(i('EXEC.SUC'));
    }

    public function destroy() {
        sys_destroy(T::$U->post, $this->table, $this->id_field);
        sys_succeed(i('DEL.SUC'));
    }
}
