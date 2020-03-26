<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function tu_get_request(){
    if(FALSE === strpos($_SERVER['REQUEST_URI'],'/index.php')){
        $str = substr($_SERVER['REQUEST_URI'],strlen($_SERVER['SCRIPT_NAME'])-strlen('/index.php'));
    }else{
        $str = substr($_SERVER['REQUEST_URI'],strlen($_SERVER['SCRIPT_NAME']));
    }
    return urldecode(explode('?',$str)[0]);
}

function tu_file_path($path){
    $dir = dirname(T::$H->FILEPATH.$path);
    file_exists($dir) OR mkdir($dir, 0755, TRUE);
    return T::$H->FILEPATH.$path;
}

function tu_put_contents($path, $ct){
    $dir = dirname(T::$H->FILEPATH.$path);
    file_exists($dir) OR mkdir($dir, 0755, TRUE);
    return file_put_contents(T::$H->FILEPATH.$path, $ct);
}


function sys_upload($field, $type, $limit, $prefix='', $upload_path=NULL, $keep_original_name=FALSE) {
    if(!$upload_path){
        $upload_path = 'upload/';
    }
    $file_type = array('image' => i('PIC'), 'word' => 'word '.i('FILE'), 'excel' => 'CSV '.i('FILE'));
    if (!isset($_FILES[$field])) {
        sys_error(i('MISS.PARAM'));
    } else {
        if ($_FILES[$field]['error'] > 0) {
            sys_error(i('UPLOAD.ERR').' - '.$_FILES[$field]['error']);
        }
    }
    if (!strstr($_FILES[$field]['type'], $type) && $type != 'all') {
        sys_error(i('UPLOAD.TYPE') .' : '. $file_type[$type] );
    }
    if ($_FILES[$field]['size'] > $limit) {
        sys_error(i('UPLOAD.LIMIT') .' : '. ($limit/1024 > 1024 ? $limit/1048576 . 'MB': $limit/1024 .'KB') );
    }
    $extension = pathinfo($_FILES[$field]['name'],PATHINFO_EXTENSION);
    $file_name =  explode('.',$_FILES[$field]['name'])[0];

    $transed = false;
    if($keep_original_name){
        if ( strtoupper ( substr ( PHP_OS ,  0 ,  3 )) ===  'WIN' ) {
            $file_name = iconv('utf-8','gbk//IGNORE',$file_name);
            $transed = true;
        }
        $save_path = $upload_path . $prefix . $file_name . date('YmdHis') . rand(1000, 9999) .'.' . $extension;
    }else{
        $save_path = $upload_path . date('YmdHis') . rand(1000, 9999) .'.' . $extension;
    }  
    $save_path = tu_file_path(str_replace('%','',$save_path));
    move_uploaded_file($_FILES[$field]['tmp_name'], $save_path);
    if($transed){
        $save_path = iconv('gbk//IGNORE','utf-8',$save_path);
    }
    return $save_path;
}