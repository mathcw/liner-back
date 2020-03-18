<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends TU_Controller {

    public $table = 'sys_config';
    public $items = ['系统配置','系统参数'];

    public function read(){
        T::$U->db->where_in('key',$this->items);
        $q = T::$U->db->get($this->table)->result_array();
        $rst=[];
        foreach ($q as $v) {
            $rst[$v['key']] = $v['key']=='系统配置' ? $v['value'] : json_decode($v['value']);
        }
        sys_succeed(NULL,$rst);
    }

    public function submit(){
        $obj = json_decode(file_get_contents('php://input'));
        $post = [
            'key'=>'系统参数',
            'value'=>empty($obj->value)?'':json_encode_unescaped($obj->value)
        ];
        
        T::$U->db->replace($this->table,$post);
        //update cache
        $q = T::$U->db->get_where('sys_config',['cache'=>1])->result_array();
        $cache = [];
        foreach ($q as $v) {
            $cache[$v['key']] = json_decode($v['value'],true);
        }
        $cache = json_encode_unescaped($cache);
        T::$U->redis->set(APP_NAME.':sys_variables',$cache);
        $auth_id = $_SESSION['auth_id'];
        T::$U->redis->del([
            APP_NAME.':pem_actions:'.$auth_id,
            APP_NAME.':pem_filters:'.$auth_id,
            APP_NAME.':pem_urls:'.$auth_id,
            APP_NAME.':view_pem:'.$auth_id
        ]);
        sys_succeed(i('SAVE.SUC'));
    }
}
