<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Action {

    public static $c = [];

    public static function init(){    
        foreach (['Org','Business','Sys','Product'] as $name) {
            require_once $name.'Action.php';
            $cat = $name.'Action';
            foreach ($cat::$c as $action => $cfg) {
                if(isset(Mod::$c[$action])){
                    sys_error($action.'-事件与模块重复');
                }
                if(isset(Action::$c[$action])){
                    sys_error($action.'-事件配置重复');
                }
                Action::$c[$action] = $cfg;
            }
        }
    }
}

Action::init();