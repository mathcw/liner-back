<?php
defined('BASEPATH') OR exit('No direct script access allowed');


function _error_filter($message){
    // var_dump($message);
    if(file_exists('dev')){
        return $message;
    }

    $m = trim($message);
    $word = explode(' ', $m);
    $word = explode(':', $word[0]);
    $word = strtolower($word[0]);

    switch ($word) {
        case 'table':
        case 'error':
            return '';
        case 'select':
        case 'insert':
        case 'update':
        case 'delete':
            return "DB $word error";
        case 'filename':
            $m = strrchr($m, '/');
            return '@ '.rtrim($m,'.php');
        case 'line':
            return explode(':', $m)[1]??'';
        case 'mysql':
            return 'DB error';
        default:
            return $message;
    }
}

function sys_ctrl_exception($exception)
{
    $message = $exception->getMessage();
    $filepath = $exception->getFile();
    $line = $exception->getLine();

    $_error =& load_class('Exceptions', 'core');
    $_error->log_exception('error', 'Exception: '.$message, $filepath, $line);

    if (empty($message))
    {
        $message = '(null)';
    }
    
    $file_name = pathinfo($filepath, PATHINFO_FILENAME);
    $message = _error_filter($message);
    sys_error("$message @ $file_name $line");
}

function sys_ctrl_error($message)
{
    if(!is_array($message)){
        $message = [$message];
    }
    if($message[0] === 'Error Number: 1062'){
        $d = $message[1];
        $p = strpos($d,"'");
        $d = substr($d,$p+1);
        $p = strpos($d,"'");
        $error = substr($d,0,$p);
        sys_error($error.' '.i('DUPLICATE'));
    }
    foreach ($message as &$v) {
        $v = _error_filter($v);
    }
    $message = implode("\n", $message);
    if(file_exists('dev')){
        ob_start();
        debug_print_backtrace();
        $trace = ob_get_clean();
        $message .= "\n".$trace;
    }
    sys_error($message);
}

function sys_runtime_error($severity, $message, $filepath, $line)
{
    if(!error_reporting()){
        return;
    }
    $_error =& load_class('Exceptions', 'core');
    $_error->log_exception($severity, $message, $filepath, $line);

    $file_name = pathinfo($filepath, PATHINFO_FILENAME);
    $message = _error_filter($message);
    sys_error("[$severity] $message @ $file_name $line");
}

function sys_shutdown_handler()
{
    if(!empty(T::$U->db)){
        while(T::$U->db->trans_rollback()){
            //noop
        }
    }
    sys_rollback_task();
}

function sys_enum_info(&$ret)
{
    if(empty($_SESSION['account_id'])){
        return;
    }
    if($_SESSION['app_name'] != APP_NAME){
        return;
    }
    if(!defined('APP_NAME')){
        return;
    }
    list($ver) = T::$U->redis->mget([
        APP_NAME.':EnumVer'
    ]);

    if($ver != $_SESSION['front_enum']){
        $ret['enum'] = $ver;
    }
}

function sys_rollback_task(){
    if(!empty(T::$ROLLBACKTASK)){
        $task = array_pop(T::$ROLLBACKTASK);
        while(!empty($task)){
            if(!empty($task['fun'])){
                $fun = $task['fun'];
                $param = [];
                if(!empty($task['param'])){
                    $param = $task['param'];
                }
                call_user_func_array($fun,[$param]);
            }
            $task = array_pop(T::$ROLLBACKTASK);
        }
    }
}

function sys_error($msg){
    
    $output = ['success'=>false, 'message'=>$msg];

    sys_enum_info($output);

    $output = json_encode_unescaped($output);

    header('Content-Type: application/json');
    echo $output;
    
    exit();
}

function sys_succeed($msg, $data = null, $extra = null) {

    $output['success'] = true;

    sys_enum_info($output);

    if ($msg){
        $output['message'] = $msg;
    }
    if ($data !== null) {
        $output['data'] = $data;
    }
    if ($extra) {
        $output = array_merge($output, $extra);
    }

    $output = json_encode_unescaped($output);

    header('Content-Type: application/json');
    echo $output;
}

function json_encode_unescaped($value){
    return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

