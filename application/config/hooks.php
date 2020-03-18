<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/


$hook['pre_system'] = function()
{
    $LOADER =& load_class('Loader', 'core');
    $LOADER->helper('sys');
    
    header("Access-Control-Allow-Origin: *");
};

$hook['post_controller'] = function()
{    
    tu_access_log();
    
    tu_update_enum();

    foreach(T::$TASK as $task){
        if(!empty($task['callback'])){
            $task = call_user_func_array($task['callback'],$task['params']);
        }
        if(empty($task['channel']) || empty($task['data'])){
            continue;
        }
        T::$U->redis->publish($task['channel'], $task['data']);
    }
    // 这里可以优化 当一次请求多个同步任务时 可以等待并一次处理
    foreach(T::$BASEDATESYNCTASK as $sync_task){
        if(!empty($sync_task['type'])&&!empty($sync_task['data'])){
            data_sync_task($sync_task['type'],$sync_task['data']);
        }
    }
};