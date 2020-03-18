<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function set_cfg_filter_where($filter){
    if(is_string($filter)){
        T::$U->db->where($filter,NULL,FALSE);
        return;
    }

    foreach ($filter as $k => $v) {

        if(is_array($v)){
            T::$U->db->where_in($k,$v);
        }else if($v === 0){
            T::$U->db->where($k,$v);
        }else{
            switch ($v) {
                case 'Self':
                    T::$U->db->where($k, $_SESSION['account_id']);  
                    break;
                default:
                    T::$U->db->where($k,$v);
                    break;
            }
        }
    }
}

function set_mod_filter_where($mod){

    if(empty(Mod::$c[$mod]['filter'])){
        return;
    }

    $filter = Mod::$c[$mod]['filter'];

    set_cfg_filter_where($filter);
}

function set_action_filter_where($action){

    if(empty(Action::$c[$action]['filter'])){
        return;
    }

    $filter = Action::$c[$action]['filter'];

    set_cfg_filter_where($filter);
}

function set_mod_search_where($mod,$get){
    if(empty(Mod::$c[$mod]['search'])){
        return ;
    }
    $search = Mod::$c[$mod]['search'];
    foreach ($search as $k => $cfg) {
        if(isset($get[$k])){
            if($get[$k] === null || $get[$k] === ''){
                continue;
            }
        }else{
            continue;
        }
        $v = $get[$k];

        if(isset($cfg['compare'])){
            $fd = empty($cfg['field']) ? $k : $cfg['field'];
            switch ($cfg['compare']) {
                case 'DateFrom':
                    T::$U->db->where($fd.' >=', $v);
                    break;
                case 'DateTo':
                    T::$U->db->where($fd.' <=', $v);
                    break;
                case 'ZeroCompare':
                    switch($v){
                        case 'gt':
                            T::$U->db->where($fd.' >', 0);
                            break;
                        case 'eq':
                            T::$U->db->where($fd.' =', 0);
                            break;
                        case 'lt':
                            T::$U->db->where($fd.' <', 0);
                            break;
                        case 'le':
                            T::$U->db->where($fd.' <=', 0);
                            break;
                    }
                    break ;
                default:
                    break;
            }
        }else{   
            $type = empty($cfg['type']) ? 'Trim' : $cfg['type'];
            switch ($type) {
                case 'Enum':
                    T::$U->db->where($k, $v);
                    break;
                case 'TrimID':
                    T::$U->db->like($k, preg_replace('/^[^1-9]+/', '', $v));
                    break;
                case 'Trim':
                    T::$U->db->like($k, trim($v));
                    break;
                default:
                    T::$U->db->like($k, $v);
                    break;
            }
        }
    }
}

function set_pem_where($mod){
    if(empty(T::$H->pem_filters[$mod])){
        return;
    }

    if(empty(Mod::$c[$mod]['auth_filter'])){
        return;
    }

    $pv_filter = T::$H->pem_filters[$mod];
    $auth_filter = Mod::$c[$mod]['auth_filter'];

    foreach ($pv_filter as $k => $v) {
        if(empty($v)){
            continue;
        }
        $type = $auth_filter[$k]['type'];

        if ( $v[0] == -1 ) {  
            switch($type){
                case 'Employee':
                    T::$U->db->where($k, $_SESSION['account_id']); 
                    break;
            }
        } else {
            T::$U->db->where_in($k, $v);
        } 
    }
}

function set_other_field_where($mod, $get, $table)
{
    $fields = T::$U->db->list_fields($table);
    $diff = array_diff(
        $fields,
        (empty(Mod::$c[$mod]['search']) ? [] : array_keys(Mod::$c[$mod]['search']))
    );

    foreach ($diff as $field) {
        if(isset($get[$field])){
            if($get[$field] !== '' && $get[$field] !== NULL){
                T::$U->db->where($field,$get[$field]);
            }
        }
    }
}


function sys_read($table, $get, $order_field='id', $order_dir='asc') { 
    
    require_once APPPATH.'core/view/mod/Mod.php';
    require_once APPPATH.'core/view/action/Action.php';

    if(empty($get['mod'])){
        sys_error(i('MISS').'mod');
    } 

    $mod = $get['mod'];
    if((isset(Mod::$c[$mod]['read']['url'])
        &&Mod::$c[$mod]['read']['url'] != tu_get_request())&&
        (!isset(Mod::$c[$mod]['perm_read'])||!in_array(tu_get_request(),Mod::$c[$mod]['perm_read']))){
        sys_error('mod'.i('ERR'));
    }

    if(!empty(T::$H->pem_actions) && !in_array($mod,T::$H->pem_actions)){
        sys_error(i('NO_PV'));
    }
    if(!empty(T::$H->pem_actions) && !empty($get['action'])){
        if(!in_array($get['action'],T::$H->pem_actions)){
            sys_error(i('NO_PV'));
        }
    }

    $limit = empty($get['limit']) ? 100 : $get['limit'] ;

    if($limit > 1000){
        $limit = 100;
    }

    $start = empty($get['start']) ? 0 : $get['start'] ;
    
    set_mod_filter_where($mod);

    if(!empty($get['action'])){
        set_action_filter_where($get['action']);
    }

    set_pem_where($mod);

    set_mod_search_where($mod,$get);

    set_other_field_where($mod,$get,$table);

    
    $sql = T::$U->db->get_compiled_select($table, false);
    $m = md5($sql);
    $total_cache = T::$U->redis->get(APP_NAME.':total_cache:'.$m);
    
    if(empty($total_cache)){
        $t1 = microtime(true);
        $total = T::$U->db->count_all_results('', false);
        $total_sql = T::$U->db->last_query();
        $t2 = microtime(true);
        $interval = $t2-$t1;
        if($interval > 1.0){
            T::$U->redis->setex(APP_NAME.':total_cache:'.$m, 3600, $total.'$$'.$interval.'$$'.$total_sql);
        }
    }else{
        $total = (int)explode('$$',$total_cache)[0];
    }
    
    //排序
    T::$U->db->order_by($order_field,$order_dir); 
    
    $items = T::$U->db->get('', $limit, $start)->result_array();
    return array($total,$items);
}

function assoc_is_readable($filters,$account,$mod,$assoc_view){

    require_once APPPATH.'core/view/mod/Mod.php';

    $user_filter = $filters[$mod];
    $auth_filter = Mod::$c[$mod]['auth_filter'];
    foreach ($user_filter as $field => $filter) {
        $type = $auth_filter[$field]['type'];
        if(in_array(-1, $filter)){
            switch($type){
                case 'Employee':
                    $expect = $account['account_id'];
                    break;
            }
            assert('$expect');
            if($assoc_view[$field] != $expect){
                return false;
            }
        }else if(!empty($filter)){
            if(!in_array($assoc_view[$field], $filter)){
                return false;
            }
        }
    }
    return true;
}
