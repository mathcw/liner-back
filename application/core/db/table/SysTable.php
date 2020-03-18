<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class SysTable
{

    public static $c = [
        'sys_config' => [[
            'key' =>    ['type'=>'*   |varchar(32)', 'text'=>['键',  'key']],
            'value' =>  ['type'=>'*   |text',        'text'=>['值',  'value']],
            'cache' =>  ['type'=>"    |tinyint(4) DEFAULT '1'",  'text'=>['缓存',  'cache']],
        ],[
            'key_uq' => 'UNIQUE KEY `key_uq` (`key`)'
        ]],

        'sys_session' => [[
            'sid' =>          ['type'=>'*   |varchar(64)', 'text'=>['会话标识',  'session id']],
            'account_id' =>   ['type'=>'ref*|',            'text'=>['用户',     'user']],
            
            'ip' =>           ['type'=>'*   |varchar(16)', 'text'=>['IP',       'IP']],
            'expire_time' =>  ['type'=>'*   |int(11)',     'text'=>['到期时间 ', 'expire time']],
            'ios_tkn' =>      ['type'=>'    |varchar(255)','text'=>['ios_tkn ', 'ios_tkn']],
        ],[
            'sid_uq' => 'UNIQUE KEY `sid_uq` (`sid`)'
        ]],

        'sys_session_app' => [[
            'sid' =>          ['type'=>'*   |varchar(64)','text'=>['会话标识',  'session id']],
            'account_id'=>    ['type'=>'ref*|'           ,'text'=>['用户','user']],
            
            'ip' =>           ['type'=>'*   |varchar(16)', 'text'=>['IP',       'IP']],
            'expire_time' =>  ['type'=>'*   |int(11)',     'text'=>['到期时间 ', 'expire time']],
            'ios_tkn' =>      ['type'=>'    |varchar(255)','text'=>['ios_tkn ', 'ios_tkn']],
        ],[
            'sid_uq' => 'UNIQUE KEY `sid_uq` (`sid`)'
        ]],

        'sys_login_log' => [[
            'id' =>           ['type'=>'id  |',            'text'=>['主键',     'identity']],
            'account_id'=>    ['type'=>'ref*|'           , 'text'=>['用户','user']],
            'ip' =>           ['type'=>'*   |varchar(64)', 'text'=>['IP',       'IP']],
            'user_agent' =>   ['type'=>'*   |varchar(128)','text'=>['user agent',  'user agent']],
            'is_app' =>       ['type'=>'    |tinyint(4)',  'text'=>['App',  'App']],
            'create_at' =>    ['type'=>'stamp1|',          'text'=>['登录时间',   'login time']],
        ],[
            'pk' => 'PRIMARY KEY (`id`)',
        ]],

        'sys_access_log' => [[
            'id' =>           ['type'=>'id  |',            'text'=>['主键',     'identity']],
            'account_id' =>   ['type'=>'ref*|',            'text'=>['用户',     'user']],
            'ip' =>           ['type'=>'*   |varchar(64)', 'text'=>['IP',       'IP']],
            'path_info' =>    ['type'=>'*   |varchar(64)', 'text'=>['访问路径',   'path info']],
            'get_param' =>    ['type'=>'*   |varchar(64)', 'text'=>['get参数',   'query string']],
            'post_param' =>   ['type'=>'*   |varchar(64)', 'text'=>['post参数',  'post string']],
            'create_at' =>    ['type'=>'stamp1|',          'text'=>['访问时间',   'access time']],
        ],[
            'pk' => 'PRIMARY KEY (`id`)',
        ]],

        'sys_sms'=>[[
            'id' =>           ['type'=>'id  |',            'text'=>['主键',     'identity']],
            'msg'=>           ['type'=>'    |varchar(255)',    'text'=>['type',    'type']],
            'data'=>          ['type'=>'    |text',        'text'=>['data',    'data']],
            'create_at' =>    ['type'=>'stamp1|',          'text'=>['访问时间', 'access time']],
        ],[
            'pk' => 'PRIMARY KEY (`id`)',
        ]]
    ];
}
