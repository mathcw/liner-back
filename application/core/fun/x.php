<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function tu_set_timer($data){
    T::$TASK[]=array('channel'=>'liner_timer','data'=>json_encode_unescaped(['data'=>$data]));
}

function tu_compose_msg($receiver, $msg_type, $msg_level, $title, $extra){
    $data = array(
        'receiver_ids' => $receiver,
        'data' => [
                'msg_type' => $msg_type,
                'msg_level' => $msg_level,
                'title' => $title,
                'extra' => $extra,
            ],
    );
    return $data;
}

function tu_send_msg($receiver, $msg_type, $msg_level, $title, $extra) {
    if(empty($receiver)){
        return;
    }
    if (!is_array($receiver)) {
        $receiver = array($receiver);
    }
    $receiver = array_unique($receiver);
    $data = tu_compose_msg($receiver, $msg_type, $msg_level, $title, $extra);
    T::$TASK[]=array('channel'=>'liner_msg','data'=>json_encode_unescaped($data)); 
}

//---------------------------异步rollback-------------------------------
function tu_push_rollback($fun,$param){
    T::$ROLLBACKTASK[]=array('fun'=>$fun,'param'=>$param);
}

function tu_pop_rollback(){
    if(!empty(T::$ROLLBACKTASK)){
        array_pop(T::$ROLLBACKTASK);
    }
}