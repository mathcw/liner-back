<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function sys_submit($post, $table, $id_fd='id') {
    
    if (empty($post[$id_fd])) {
        $action = 'create';
    } else {
        $action = 'update';
    }

    $rec = sys_field_collect($post, $table, $action);
    if (empty($rec)) {
        sys_error(i('NO_DATA'));
    }
    if ($action === 'create') {
        T::$U->db->insert($table, $rec);
    } else {
        T::$U->db->update($table, $rec, array($id_fd => $post[$id_fd]));
    }
    return $rec;
}

function sys_create($post, $table) {
    return sys_submit($post, $table);
}

function sys_update($post, $table, $id_fd='id') {
    return sys_submit($post, $table, $id_fd);
}

function sys_update_batch($post, $table, $id_fd='id'){

    $action = 'update';
    foreach ($post as $record) {
        $rec = sys_field_collect($record, $table, $action);
        if (empty($rec)) {
            sys_error(i('NO_DATA'));
        }
        if(!empty($record[$id_fd]))
            $rec[$id_fd] = $record[$id_fd];
        $update[] = $rec;
    }
    if(!empty($update)){
        T::$U->db->update_batch($table, $update, $id_fd);
        return $update;
    }  
}

function sys_update_batch_complex($update,$table,$where_arr){
    if(!empty($update)){
        $where_cond = "";
        $update_sql = "set ";
        $or = " ( ";
        foreach($where_arr as $index =>$item){
            $where_sql = "";
            $and = " ";
            foreach($item as $field => $v){
                $where_sql = $where_sql.$and."`".$field ."` = '".$v."'";
                $and = " and ";
            }
            $where_cond = $where_cond .$or.$where_sql;
            $or = " ) or ( ";
        }
        $and = " "; 
        foreach($update as $field => $value){
            $update_sql = $update_sql .$and . $field . " = " .$value;
            $and = " and ";
        }
        $sql = "update `".$table ."` ".$update_sql .' where '.$where_cond . ' )';
        T::$U->db->query($sql);
    }
}

function sys_create_batch($post, $table){

    $action = 'create';
    $keys = [];
    $insert = [];
    foreach ($post as $record) {
        $rec = sys_field_collect($record, $table, $action);
        $keys = array_merge($keys,array_keys($rec));
        if (empty($rec)) {
            sys_error(i('NO_DATA'));
        }
        $insert[] = $rec;
    }
    foreach ($insert as &$v) {
        foreach ($keys as $k) {
            if(!isset($v[$k])){
                $v[$k] = '';
            }
        }
    }
    if(!empty($insert)){
        T::$U->db->insert_batch($table, $insert);
        return $insert;
    }  
}
function sys_replace($post, $table, $fields, $id='id'){
    if(empty($post[$id])){
        sys_create($post, $table, $fields);
    }else{
        sys_update($post, $table, $fields, $id);
    }
}

function sys_get_field_cfg($cfg){
    $type = trim(explode('|',$cfg['type'])[0]);
    $text = $cfg['text'][I18n::$lang];
    $extra = empty($cfg['extra'])?[]:$cfg['extra'];
    return array($type,$text,$extra);
}

function sys_standard_set(&$set, $action, $cfg, $post, $field){

    list($type,$text,$extra) = sys_get_field_cfg($cfg);

    //                      create     update
    // 字段未提交   必填       报错        不变
    // 字段未提交   非必填     默认值      不变
    // 字段未填     必填       报错       报错
    // 字段未填     非必填     默认值      置空
    if( !isset($post[$field]) ){
        if($action === 'create'){
            if(strstr($type,'*')){
                sys_error(i('MISS').$text);
            }
        }
    }else if( $post[$field] === '' || $post[$field] === NULL ){
        if(strstr($type,'*')){
            sys_error(i('MISS').$text);
        }

        if($action === 'update'){
            $set[$field] = $post[$field];
        }
    }else{
        $set[$field] = trim($post[$field]);
    }
}

//不再post form，不再需要
function sys_file_set(&$set, $action, $cfg, $post, $field){
    
    list($type,$text,$extra) = sys_get_field_cfg($cfg);

    if(empty($_FILES[$field]['name'])){
        if($action === 'create'){
            if(strstr($type,'*')){
                sys_error(i('MISS').$text);
            }
        }
    }else{
        $save_path = sys_upload($field, $extra['file_type'], $extra['limit'], empty($extra['prefix'])?'':$extra['prefix']);
        $set[$field] = $save_path;
    }
}

function sys_field_collect($post, $table, $action) {

    require_once APPPATH.'core/db/DB.php';
    $fields = DB::$tables[$table][0];

    $set = [];

    foreach ($fields as $field => $cfg) {

        list($type,$text,$extra) = sys_get_field_cfg($cfg);

        switch ($type) {
            case 'id':
            case 'stamp':
            case 'stamp1':
                continue;
                break;
            case 'now':
                if($action === 'create'){
                    $set[$field] = date('Y-m-d');
                }
                break;
            case 'self':
                if($action === 'create'){
                    $set[$field] = $_SESSION['account_id'];
                }
                break;
            case 'ref*': //为解决update传0的问题
            case 'gtz':
            case 'nz':
                if(isset($post[$field])) {
                    if($type == 'nz'){
                        $err = ($post[$field] == 0);
                    }else{
                        $err = !($post[$field] > 0);
                    }
                    if($err){
                        sys_error($text.i('ERR'));
                    }
                    $set[$field] = trim($post[$field]);
                }else if($action === 'create'){
                    sys_error(i('MISS').$text);
                }
                break;
            case 'json':
            case 'json*':
                if(isset($post[$field]) && is_array($post[$field])){
                    $post[$field] = json_encode_unescaped($post[$field]);
                }
            default:
                sys_standard_set($set, $action, $cfg, $post, $field);
                break;
        }
    }

    return $set;
}


function sys_destroy($post, $table, $id_fd='id') {
    if (empty($post[$id_fd])) {
        sys_error(i('MISS.PARAM'));
    }
    T::$U->db->delete($table, array($id_fd => $post[$id_fd]));
}

function sys_uniq_check($post,$table,$field,$id_fd='id'){
    if(!empty($post[$id_fd])){
        T::$U->db->where($id_fd.'!=',$post[$id_fd]);
    }
    $q = T::$U->db->get_where($table,[$field=>$post[$field]])->row_array();
    if(!empty($q)){
        sys_error(i18n_field($table,$field).' '.i('DUPLICATE'));
    }
}
