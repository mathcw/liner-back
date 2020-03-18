<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function tu_user_enum(){
    return [];
}

function tu_update_enum($force=false) {
    $class = T::$U->router->fetch_class();
    $method = T::$U->router->fetch_method();
    if(!$force && !in_array($method,['submit','toggle','destroy'])){
        return;
    }
    
    $types = array(
        'Auth'=>                    ['table'=>'auth',           'filter'=>['state'=>1],'key_fd'=>'id','value_fd'=>'name'],
        'Account' =>                ['table'=>'account',  'filter'=>['state' => 1],'key_fd'=>'id','value_fd'=>'name'],
        'CruiseCompany' =>          ['table'=>'cruise_company',  'filter'=>['state' => 1],'key_fd'=>'id','value_fd'=>'name'],
    );
    $tables = [];
    foreach ($types as $type => $cfg) {
        $tables[$cfg['table']] = 1;
    }
  
    if($force || (!empty(T::$C->table) && !empty($tables[T::$C->table]))) {
        foreach ($types as $type => $cfg) {
            $q = T::$U->db->get_where($cfg['table'],$cfg['filter'])->result_array();

            $arr = explode('||',$cfg['value_fd']);

            if(count($arr) == 1){

                $enum[$type] = array_column($q,$cfg['value_fd'],$cfg['key_fd']);

            }else{

                $map = [];
                foreach ($q as  $row) {
                    $str = '';
                    foreach ($arr as $item) {
                        if(substr($item,0,1) == '$'){
                            $var = substr($item,1);
                            $str .= $row[$var];
                        }else{
                            $str .= $item;
                        }
                    }
                    $map[$row[$cfg['key_fd']]] = $str;
                }
                $enum[$type] = $map;
            }
        }

        $data = json_encode_unescaped($enum);
        $ver = md5($data);
        $enum['ver'] = $ver;
        $enum['success'] = true;
        $output = json_encode_unescaped($enum);

        $old_ver = T::$U->redis->get(APP_NAME.':EnumVer');

        if($old_ver != $ver){
            tu_put_contents('cache/Enum.js',  $output);
            T::$U->redis->set(APP_NAME.':EnumVer', $ver);
        }

    }

}

