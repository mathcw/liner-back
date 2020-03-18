<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 */
class BusinessTable
{
    public static $c = [
        'comm_dict' => [[
            'id' => ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'type_id' => ['type' => 'ref*|', 'text' => ['类型', 'type']],
            'name' => ['type' => '*   |varchar(64)', 'text' => ['名称', 'name']],
            'code' => ['type' => '    |varchar(16)', 'text' => ['代码', 'code']],
            'additional' => ['type' => '    |varchar(255)', 'text' => ['附加', 'additional']],
            'account_id' => ['type' => 'self|', 'text' => ['创建人', 'account id']],
            'state' => ['type' => "    |tinyint(4) DEFAULT '1'", 'text' => ['状态 ', 'state']],
            'last_update' => ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'type_name_uq' => 'UNIQUE KEY `type_name_uq` (`type_id`,`name`,`additional`)',
        ]],

        'comm_city' => [[
            'id' => ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'account_id' => ['type' => 'self|', 'text' => ['创建人', 'account id']],
            'country' => ['type' => '*   |varchar(16)', 'text' => ['国家', 'country']],
            'name' => ['type' => '*   |varchar(45)', 'text' => ['城市', 'city']],
            'code' => ['type' => '    |varchar(16)', 'text' => ['城市代码', 'code']],
            'state' => ['type' => "    |tinyint(4) DEFAULT '1'", 'text' => ['状态 ', 'state']],
            'last_update' => ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'country_name_uq' => 'UNIQUE KEY `country_name_uq` (`country`,`name`)',
        ]],

        'cruise_company' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'account_id' =>         ['type' => 'self|', 'text' => ['创建人', 'account id']],
            'name' =>               ['type' => '*   |varchar(256)', 'text' => ['名称', 'name']],
            'banner'=>              ['type' => '    |varchar(256)', 'text' => ['banner', 'banner']],
            'des' =>                ['type' => '    |text', 'text' => ['内容', 'context']],
            'des_html' =>           ['type' => '    |text', 'text' => ['内容html', 'html']],
            'ship_num'=>            ['type' => '    |int(11)', 'text' => ['下属船只数', 'ship number']],
            'state' =>              ['type' => "    |tinyint(4) DEFAULT '1'", 'text' => ['状态 ', 'state']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
        ]],

        'cruise_ship' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'account_id' =>         ['type' => 'self|', 'text' => ['创建人', 'account id']],
            'name' =>               ['type' => '*   |varchar(255)', 'text' => ['名称', 'name']],
            'level'=>               ['type' => "    |tinyint(4) DEFAULT '1'",'text'=>['星级','level']],
            'build_time'=>          ['type' =>'     |varchar(255)','text'=>['建造年份','build time']],
            'capacity'=>            ['type' =>'     |int(11)','text'=>['载客量','capacity']],
            'room_number'=>         ['type' =>'     |int(11)','text'=>['房间','room number']],
            'place_of_build'=>      ['type'=>'ref   |','text'=>['建造地','place of build']],
            'master'=>              ['type'=>'ref   |','text'=>['船籍','master']],
            'date_of_use'=>         ['type'=>'      |varchar(255)','text'=>['使用时间','date of use']],
            'length'=>              ['type'=>'      |int(11)','text'=>['长度','length']],
            'width'=>               ['type'=>'      |int(11)','text'=>['宽度','width']],
            'speed'=>               ['type'=>'      |int(11)','text'=>['航速','speed']],
            'state' =>              ['type' => "    |tinyint(4) DEFAULT '1'", 'text' => ['状态 ', 'state']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
        ]],
        
        //简介
        'ship_des' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'ship_id'=>             ['type' => 'ref*  |','text' => ['邮轮', 'ship']],
            'des' =>                ['type' => '    |text', 'text' => ['内容', 'context']],
            'des_html' =>           ['type' => '    |text', 'text' => ['内容html', 'html']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'ship_uq' => 'UNIQUE KEY `ship_uq` (`ship_id`)'
        ]],
        //客房
        'ship_room' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'ship_id'=>             ['type' => 'ref*  |','text' => ['邮轮', 'ship']],
            'des' =>                ['type' => '    |text', 'text' => ['内容', 'context']],
            'des_html' =>           ['type' => '    |text', 'text' => ['内容html', 'html']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'ship_uq' => 'UNIQUE KEY `ship_uq` (`ship_id`)'
        ]],
        //food
        'ship_food' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'ship_id'=>             ['type' => 'ref*  |','text' => ['邮轮', 'ship']],
            'des' =>                ['type' => '    |text', 'text' => ['内容', 'context']],
            'des_html' =>           ['type' => '    |text', 'text' => ['内容html', 'html']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'ship_uq' => 'UNIQUE KEY `ship_uq` (`ship_id`)'
        ]],
        //game
        'ship_game' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'ship_id'=>             ['type' => 'ref*  |','text' => ['邮轮', 'ship']],
            'des' =>                ['type' => '    |text', 'text' => ['内容', 'context']],
            'des_html' =>           ['type' => '    |text', 'text' => ['内容html', 'html']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'ship_uq' => 'UNIQUE KEY `ship_uq` (`ship_id`)'
        ]],
        //布局
        'ship_layout' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'ship_id'=>             ['type' => 'ref*  |','text' => ['邮轮', 'ship']],
            'des' =>                ['type' => '    |text', 'text' => ['内容', 'context']],
            'des_html' =>           ['type' => '    |text', 'text' => ['内容html', 'html']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'ship_uq' => 'UNIQUE KEY `ship_uq` (`ship_id`)'
        ]],
    ];
}
