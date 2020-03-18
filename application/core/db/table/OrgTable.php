<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class OrgTable
{

    public static $c = [
        //账号
        'account' => [[
            'id' =>           ['type'=>'id  |',            'text'=>['主键',     'identity']],
            'account' =>      ['type'=>'*   |varchar(32)', 'text'=>['账号',     'account']],
            'password' =>     ['type'=>"   |varchar(32) DEFAULT 'e10adc3949ba59abbe56e057f20f883e'", 'text'=>['密码',     'password']],
            'name' =>         ['type'=>'*   |varchar(32)', 'text'=>['姓名',     'name']],
            'mobile'=>        ['type'=>'    |varchar(64)', 'text'=>['手机',     'mobile']],
            'state' =>        ['type'=>"    |tinyint(4) DEFAULT '1'",'text'=>['状态 ', 'state']],
            'auth_id'=>       ['type'=>'ref|',         'text'=>['权限','auth']],
            'last_update' =>  ['type'=>'stamp|',           'text'=>['最后更新',   'last update']],
        ],[
            'pk' => 'PRIMARY KEY (`id`)',
            'account_uq' => 'UNIQUE KEY `account_uq` (`account`)'
        ]],

        'auth' => [[
            'id' =>           ['type'=>'id  |',            'text'=>['主键',     'identity']],
            'creator_id'=>    ['type'=>'self|',         'text'=>['创建人','creator']],
            'name' =>         ['type'=>'*   |varchar(16)', 'text'=>['权限名称', 'authority name']],
            'scope' =>        ['type'=>'*   |varchar(64)', 'text'=>['适用范围', 'scope']],
            'actions' =>      ['type'=>'json|text',        'text'=>['权限',     'permission']],
            'filters' =>      ['type'=>'json|text',        'text'=>['限定',     'visiable']],
            'is_all_sale'=>   ['type'=>"    |tinyint(4) DEFAULT '0'",'text'=>['是否适用全部商家',     'all sale']],
            'state' =>        ['type'=>"    |tinyint(4) DEFAULT '1'",'text'=>['状态 ', 'state']],
            'create_at' =>    ['type'=>'stamp1|',           'text'=>['创建日期',   'create at']],
            'last_update' =>  ['type'=>'stamp|',           'text'=>['最后更新',   'last update']],
        ],[
            'pk' => 'PRIMARY KEY (`id`)',
            'name_uq' => 'UNIQUE KEY `name_uq` (`name`)'
        ]],

    ];
}
