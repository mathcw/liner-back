<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function tu_submit_urls()
{
    if(!empty(T::$H->submit_urls)){
        return;
    }

    $submit_urls = [];
    //mod perm_submit
    require_once APPPATH.'core/view/mod/Mod.php';

    foreach (Mod::$c as $cfg) {
        if(!empty($cfg['perm_submit'])){
            foreach ($cfg['perm_submit'] as $u) {
                $submit_urls[$u] = 1;
            }
        }
    }
    //action submit 
    require_once APPPATH.'core/view/action/Action.php';

    foreach (Action::$c as $cfg) {
        if(!empty($cfg['submit']['url'])){
            $submit_urls[$cfg['submit']['url']] =1 ;
            if(!empty($cfg['perm_submit'])){
                foreach ($cfg['perm_submit'] as $u) {
                    $submit_urls[$u] = 1;
                }
            }
        }
    }

    T::$H->submit_urls = $submit_urls;
}

function tu_read_urls()
{
    if(!empty(T::$H->read_urls)){
        return;
    }

    $read_urls = [];
    require_once APPPATH.'core/view/mod/Mod.php';

    foreach (Mod::$c as $cfg) {
        if(!empty($cfg['read']['url'])){
            $read_urls[$cfg['read']['url']] = 1;
        }
        if(!empty($cfg['perm_read'])){
            foreach ($cfg['perm_read'] as $u) {
                $read_urls[$u] = 1;
            }
        }
    }
    require_once APPPATH.'core/view/action/Action.php';

    foreach (Action::$c as $cfg) {
        if(!empty($cfg['read']['url'])){
            $read_urls[$cfg['read']['url']] = 1;
        }
        if(!empty($cfg['perm_read'])){
            foreach ($cfg['perm_read'] as $u) {
                $read_urls[$u] = 1;
            }
        }
    }

    T::$H->read_urls = $read_urls;
}

function tu_pub_init(){

    $sys_variables = T::$U->redis->get(APP_NAME.':sys_variables');
    $set = [];
    if(empty($sys_variables)){
        $q = T::$U->db->get_where('sys_config',['cache'=>1])->result_array();
        foreach ($q as $v) {
            T::$H->sys_variables[$v['key']] = json_decode($v['value'],true);
        }
        $set[APP_NAME.':sys_variables'] = json_encode_unescaped(T::$H->sys_variables);
    }else{
        T::$H->sys_variables = json_decode($sys_variables,true);
    }

    if(!empty($set)){
        T::$U->redis -> mset ( $set );  
    }
}

function tu_user_init(){
    if (empty($_SESSION['auth_id'])) {
        sys_error(i('MISS.PV'));
    }
    if(!empty($_SESSION['lang'])){
        set_lang($_SESSION['lang']);
    }

    $auth_id = $_SESSION['auth_id'];

    if(!empty(T::$U->get['front_enum'])){
        $_SESSION['front_enum'] = T::$U->get['front_enum'];
    }

    $auth_table = 'auth';

    list($pem_actions, $pem_filters, $pem_urls,$sys_variables) = T::$U->redis->mget([
        APP_NAME.':pem_actions:'.$auth_id,
        APP_NAME.':pem_filters:'.$auth_id,
        APP_NAME.':pem_urls:'.$auth_id,
        APP_NAME.':sys_variables'
    ]);
    //未缓存
    if(empty($pem_actions) || empty($pem_filters)){
        $pem = T::$U->db->get_where($auth_table, array('id' => $auth_id))->row_array();
        $pem_actions = empty($pem['actions']) ? '[]' : $pem['actions'];
        $pem_filters = empty($pem['filters']) ? '{}' : $pem['filters'];

        $set[APP_NAME.':pem_actions:'.$auth_id] = $pem_actions;
        $set[APP_NAME.':pem_filters:'.$auth_id] = $pem_filters;
    }  

    T::$H->pem_actions=json_decode($pem_actions,true);
    T::$H->pem_filters=json_decode($pem_filters,true);
    if(empty(T::$H->pem_actions)){
        sys_error(i('MISS.PV'));
    }

    if(empty($pem_urls)){        
        require_once APPPATH.'core/view/mod/Mod.php';
        require_once APPPATH.'core/view/action/Action.php';
        $urls = [];

        foreach (T::$H->pem_actions as $action) {
            if(!empty(Mod::$c[$action]['read']['url'])){
                $urls[Mod::$c[$action]['read']['url']] = 1;
            }
            if(!empty(Mod::$c[$action]['submit']['url'])){
                $urls[Mod::$c[$action]['submit']['url']] = 1;
            }
            if(!empty(Action::$c[$action]['read']['url'])){
                $urls[Action::$c[$action]['read']['url']] = 1;
            }
            if(!empty(Action::$c[$action]['submit']['url'])){
                $urls[Action::$c[$action]['submit']['url']] = 1;
            }
            if(!empty(Mod::$c[$action]['perm_read'])){
                foreach (Mod::$c[$action]['perm_read'] as $u) {
                    $urls[$u] = 1;
                }
            }
            if(!empty(Mod::$c[$action]['perm_submit'])){
                foreach (Mod::$c[$action]['perm_submit'] as $u) {
                    $urls[$u] = 1;
                }
            }
            if(!empty(Action::$c[$action]['perm_read'])){
                foreach (Action::$c[$action]['perm_read'] as $u) {
                    $urls[$u] = 1;
                }
            }
            if(!empty(Action::$c[$action]['perm_submit'])){
                foreach (Action::$c[$action]['perm_submit'] as $u) {
                    $urls[$u] = 1;
                }
            }
        }
        foreach (Mod::$c as $cfg) {
            if(!empty($cfg['public'])){
                $urls[$cfg['read']['url']] = 1;
            }
            if(!empty($cfg['perm_submit'])){
                foreach ($cfg['perm_submit'] as $u) {
                    $urls[$u] = 1;
                }
            }
        }
        T::$H->pem_urls = array_keys($urls);
        $set[APP_NAME.':pem_urls:'.$auth_id] = json_encode_unescaped(T::$H->pem_urls);
    }else{
        T::$H->pem_urls = json_decode($pem_urls,true);
    }

    if(empty($sys_variables)){
        $q = T::$U->db->get_where('sys_config',['cache'=>1])->result_array();
        foreach ($q as $v) {
            T::$H->sys_variables[$v['key']] = json_decode($v['value'],true);
        }
        $set[APP_NAME.':sys_variables'] = json_encode_unescaped(T::$H->sys_variables);
    }else{
        T::$H->sys_variables = json_decode($sys_variables,true);
    }

    if(!empty($set)){
        T::$U->redis -> mset ( $set );  
    }
}

function tu_entry_data(){
    $auth_id = $_SESSION['auth_id'];
    $view_pem = T::$U->redis->get(APP_NAME.':view_pem:'.$auth_id);
    if(empty($view_pem)){
        $view_pem =  tu_get_entry_pem(true);
        $set[APP_NAME.':view_pem:'.$auth_id] = json_encode_unescaped($view_pem);
    }else{
        $view_pem = json_decode($view_pem,true);
    }

    if(!empty($set)){
        T::$U->redis->mset($set);
    }
    $init = ['authority'=>$view_pem['authority']];
    $init['enum_ver'] = T::$U->redis->get(APP_NAME.':EnumVer');
    if(empty($init['enum_ver'])){
        tu_update_enum(true);
        $init['enum_ver'] = T::$U->redis->get(APP_NAME.':EnumVer');
    }
    $init['pem_filters'] = T::$H->pem_filters;
    return $init;
}

function tu_get_entry_pem($apply_auth){
    
    $mods = [];
    $authority = [];
    require_once APPPATH.'core/view/mod/Mod.php';
    foreach (Mod::$c as $mod => $cfg) {

        if(!empty($cfg['public'])){
            $authority[] = $mod;
        }
        if($apply_auth && in_array($mod,T::$H->pem_actions)){
            if(empty($cfg['type'])){
                continue;
            }
            $authority[] = $mod;
        }
    }

    require_once APPPATH.'core/view/action/Action.php';
    foreach(Action::$c as $action => $cfg){
        if($apply_auth && in_array($action,T::$H->pem_actions)){
            $authority[] = $action;
        }
    }

    return ['authority'=>$authority];
}

