<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mod {

    public static $c = [];
    public static $a = [];

    public static function init(){    
        foreach (['Org','Home','Business','Sys','Product'] as $name) {
            require_once $name.'Mod.php';
            $cat = $name.'Mod';
            foreach ($cat::$c as $mod => $cfg) {
                if(isset(Mod::$c[$mod])){
                    sys_error($mod.'-模块重复');
                }
                if(!empty($cfg['action'])){
                    foreach ($cfg['action'] as $action => $v) {
                        if(isset(Mod::$a[$action])){
                            sys_error($action.'-事件重复');
                        }
                        Mod::$a[$action] = 1;
                    }
                }
                Mod::$c[$mod] = $cfg;
            }
        }
    }
}


Mod::init();
