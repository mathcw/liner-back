<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends TU_Controller {
    public $table = 'auth';
    
    private function get_mods(){
        $mods = [];
        require_once APPPATH.'core/view/mod/Mod.php';
        foreach (Mod::$c as $mod => $cfg) {
            $mods[$mod] = ['auth_filter'=>$cfg['auth_filter']??[]];    
        }
        return $mods;
    }

    public function read_convert(&$items) {
        $ids = array_column($items, 'id');
        $employee_map = [];
        if(!empty($ids)) {
            T::$U->db->select('auth_id, count(id) employee_num');
            T::$U->db->where_in('auth_id', $ids);
            T::$U->db->group_by('auth_id');
            $employee_map = T::$U->db->get_where('account')->result_array();
            $employee_map = array_column($employee_map, 'employee_num', 'auth_id');

        }
        foreach ($items as &$item) {
            $item['employee_num'] = empty($employee_map[$item['id']])?0:$employee_map[$item['id']];
        }
    }

    public function read_new(){
        $data = ['menu'=>$this->get_mods(),'auth'=>['actions'=>[],'filters'=>new stdClass(),'type'=>1]];
        sys_succeed(null,$data);
    }

    public function read_modify() {
    	$get = T::$U->get;
    	if(empty($get['id'])){
    		sys_error(i('MISS.PARAM'));
    	}
    	$pem = T::$U->db->get_where($this->table,['id'=>$get['id']])->row_array();
    	if(empty($pem)){
    		sys_error(i('REC.NOT_EXIST'));
    	}

        $pem['actions'] = json_decode($pem['actions'],true);
        $pem['filters'] = json_decode($pem['filters']);
        if(empty($pem['actions'])){
            $pem['actions'] = [];
        }
        if(empty($pem['filters'])){
            $pem['filters'] = new stdClass();
        }

    	$data = ['menu'=>$this->get_mods(),'auth'=>$pem];
		sys_succeed(null,$data);
    }

    public function read_new_supplier_auth(){
        $data = ['menu'=>$this->get_mods(),'auth'=>['actions'=>[],'filters'=>new stdClass(),'type'=>2]];
        sys_succeed(null,$data);
    }

    public function read_modify_supplier_auth() {
    	$get = T::$U->get;
    	if(empty($get['id'])){
    		sys_error(i('MISS.PARAM'));
    	}
    	$pem = T::$U->db->get_where($this->table,['id'=>$get['id']])->row_array();
    	if(empty($pem)){
    		sys_error(i('REC.NOT_EXIST'));
    	}

        $pem['actions'] = json_decode($pem['actions'],true);
        $pem['filters'] = json_decode($pem['filters']);
        if(empty($pem['actions'])){
            $pem['actions'] = [];
        }
        if(empty($pem['filters'])){
            $pem['filters'] = new stdClass();
        }

    	$data = ['menu'=>$this->get_mods(),'auth'=>$pem];
		sys_succeed(null,$data);
    }


    public function submit(){
        $post = T::$U->post;
        require_once APPPATH.'core/view/mod/Mod.php';
        require_once APPPATH.'core/view/action/Action.php';
        $actions = [];
        foreach(Action::$c as $action =>$action_cfg){
            $actions[] = $action;
        }
        foreach(Mod::$c as $mod =>$mod_cfg){
            $actions[] = $mod;
        }
        $actions = array_values(array_intersect($post['actions'],$actions));

        $new_filters = [];

        foreach ($post['filters'] as $mod => $mod_cfg) {
            if(in_array($mod,$actions)){
                $new_cfg=[];
                foreach ($mod_cfg as $fd => $read_cfg) {
                    if(isset(Mod::$c[$mod]['auth_filter'][$fd])){
                        $new_cfg[$fd] = $read_cfg;
                    }
                }
            }
            $new_filters[$mod] = $new_cfg;
        }
        $post['filters'] = $new_filters;

        if(!empty($post['filters'])){
            $mods = array_keys($post['filters']);
            $diff = array_diff($mods,$actions);
            if(!empty($diff)){
                foreach ($diff as $mod) {
                    unset($post['filters'][$mod]);
                }
            }
        }

        if (empty($post['id'])) {
            $action = 'create';
        } else {
            $action = 'update';
        }
    
        $rec = sys_field_collect($post, $this->table, $action);
        if (empty($rec)) {
            sys_error(i('NO_DATA'));
        }
        T::$U->db->trans_start();
        if ($action === 'create') {
            T::$U->db->insert($this->table, $rec);
            $id = T::$U->db->insert_id();
        } else {
            T::$U->db->update($this->table, $rec, ['id'=>$post['id']]);
            $id = $post['id'];
        }

        T::$U->db->trans_complete();
        sys_succeed(i('SAVE.SUC'));

        if(!empty(T::$U->post['id'])){
            $auth_id = T::$U->post['id'];
            T::$U->redis->del([
                APP_NAME.':pem_actions:'.$auth_id,
                APP_NAME.':pem_filters:'.$auth_id,
                APP_NAME.':pem_urls:'.$auth_id,
                APP_NAME.':view_pem:'.$auth_id
            ]);
        }
    }

    public function copy_submit(){
        $post = T::$U->post;
        unset($post['id']);
        require_once APPPATH.'core/view/mod/Mod.php';
        require_once APPPATH.'core/view/action/Action.php';
        $actions = [];
        foreach(Action::$c as $action =>$action_cfg){
            $actions[] = $action;
        }
        foreach(Mod::$c as $mod =>$mod_cfg){
            $actions[] = $mod;
        }
        $actions = array_values(array_intersect($post['actions'],$actions));
        require_once APPPATH.'core/view/mod/Mod.php';
        $new_filters = [];
        foreach (T::$U->post['filters'] as $mod => $mod_cfg) {
            if(in_array($mod,$actions)){
                $new_cfg=[];
                foreach ($mod_cfg as $fd => $read_cfg) {
                    if(isset(Mod::$c[$mod]['auth_filter'][$fd])){
                        $new_cfg[$fd] = $read_cfg;
                    }
                }
                $new_filters[$mod] = $new_cfg;
            }
        }
        $post['filters'] = $new_filters;
        $action = 'create';
    
        $rec = sys_field_collect($post, $this->table, $action);
        if (empty($rec)) {
            sys_error(i('NO_DATA'));
        }

        T::$U->db->trans_start();
        T::$U->db->insert($this->table, $rec);

        T::$U->db->trans_complete();
        sys_succeed(i('SAVE.SUC'));
    }

    public function toggle($field) {
        $post = T::$U->post;
        if(empty($post['id']) || !isset($post[$field])){
            sys_error(i('MISS.PARAM'));
        }
        $set = 1;
        if($post[$field]>0){
            $set = 0;
        }
        T::$U->db->trans_start();
            T::$U->db->update($this->table,[$field=>$set],['id'=>$post['id']]);
            $log = [
                'auth_id' => $post['id'],
                'before' => $post[$field],
                'after' => $set,
                'field' => $field,
                'log_type' => LOG_UPDATE,
            ];
            sys_create($log,'auth_log');
        T::$U->db->trans_complete();
        sys_succeed(i('EXEC.SUC'));
    }

    public function destroy() {
        $post = T::$U->post;
        if(empty($post['id'])){
            sys_error(i('MISS.PARAM'));
        }
        T::$U->db->trans_start();
        T::$U->db->delete('auth',['id'=>$post['id']]);
        T::$U->db->trans_complete();
        sys_succeed(i('DEL.SUC'));
    }

}