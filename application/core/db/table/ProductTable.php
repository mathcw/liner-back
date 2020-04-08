<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class ProductTable
{

    public static $c = [
        //产品
        'product' => [[
            'id' =>             ['type'=>'id  |',            'text'=>['主键',     'identity']],
            'name'=>            ['type'=>'    |varchar(255)','text'=>['产品名称',  'name']],
            'cruise_company_id'=> ['type'=>'ref*|',            'text'=>['邮轮公司','cruise company']],
            'ship_id'=>         ['type'=>'ref*|',            'text'=>['船只','ship id']],
            'pd_num'=>          ['type'=>'    |varchar(255)','text'=>['船次编号','number']],
            'dep_city_id'=>     ['type'=>'ref*|',            'text'=>['出发城市','dep city']],
            'destination'=>     ['type'=>'ref*|',            'text'=>['目的地','destination']],
            'day'=>             ['type'=>'    |int(11)',     'text'=>['天数','day']],
            'night'=>           ['type'=>'    |int(11)',     'text'=>['晚数','night']],
            'kind'=>            ['type'=>'   *|tinyint(4)',  'text'=>['产品种类','kind']],
            'group_count'=>     ['type'=>'    |int(11)',     'text'=>['团期数','group count']],
            'is_recom'=>        ['type'=>'    |tinyint(4)',  'text'=>['是否推荐','recom']],    
            'last_update' =>    ['type'=>'stamp|',           'text'=>['最后更新',   'last update']],
        ],[
            'pk' => 'PRIMARY KEY (`id`)',
        ]],

        // 单船票行程
        'ticket_itin'  => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'product_id'=>          ['type' => 'ref*  |','text' => ['产品', 'ship']],
            'order'=>               ['type' => '    *|int(11)','text'=>['顺序','order']],
            'dep_city'=>            ['type'=>'      |varchar(127)',            'text'=>['出发城市','dep city']],
            'destination'=>         ['type'=>'      |varchar(127)',            'text'=>['目的地','destination']],
            'des'=>                 ['type'=>  '     |text','text'=>['行程描述','des']],
            'arr_time'=>            ['type'=>  '     |varchar(127)','text'=>['抵达时间','arr time']],
            'level_time'=>          ['type'=>  '     |varchar(127)','text'=>['离港时间','level time']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'product_idx'=>      'KEY `product_idx` (`product_id`)' 
        ]],

        // 团队游
        'product_pic' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'product_id'=>          ['type' => 'ref*  |','text' => ['产品', 'ship']],
            'pic' =>                ['type' => '    |text', 'text' => ['图片', 'context']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'product_idx'=>      'KEY `product_idx` (`product_id`)' 
        ]],
        
        'product_itin' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'product_id'=>          ['type' => 'ref*  |','text' => ['产品', 'ship']],
            'order'=>               ['type' => '    *|int(11)','text'=>['顺序','order']],
            'dep_city'=>            ['type'=>'       |varchar(127)', 'text'=>['出发城市','dep city']],
            'destination'=>         ['type'=>'       |varchar(127)',  'text'=>['目的地','destination']],
            'arr_time'=>            ['type'=>  '     |varchar(127)','text'=>['抵达时间','arr time']],
            'level_time'=>          ['type'=>  '     |varchar(127)','text'=>['离港时间','level time']],
            'breakfast'=>           ['type' => '     |varchar(127)','text'=>['早','breakfast']],
            'lunch'=>               ['type' => '     |varchar(127)','text'=>['中','lunch']],
            'dinner'=>              ['type' => '     |varchar(127)','text'=>['晚','dinner']],
            'accommodation'=>       ['type' => '     |varchar(127)','text'=>['住宿','accommodation']],
            'des'=>                 ['type'=>  '     |text',  'text'=>['简介','des']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'product_idx'=>      'KEY `product_idx` (`product_id`)' 
        ]],

        'itin_pic' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'product_id'=>          ['type' => 'ref*  |','text' => ['产品', 'ship']],
            'itin_id'=>             ['type' => 'ref*  |','text' => ['行程', 'ship']],
            'pic' =>                ['type' => '    |text', 'text' => ['图片', 'context']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'itin_idx'=>      'KEY `itin_idx` (`itin_id`)' 
        ]],

        // 产品明细
        'product_detail'=>[[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'product_id'=>          ['type' => 'ref*  |','text' => ['产品', 'ship']],
            'bright_spot'=>         ['type' => '    |text','text'=>['行程亮点','bright spot']],
            'book_info'=>           ['type' => '    |text','text'=>['预定须知','book info']],
            'fee_info'=>            ['type' => '     |text','text'=>['旅游费用','fee info']],
            'fee_include'=>         ['type' => '     |text','text'=>['费用包含','fee include']],
            'fee_exclude'=>         ['type' => '     |text','text'=>['费用不包含','fee exclude']],
            'cancel_info'=>         ['type' => '     |text','text'=>['取消条款','cancel info']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'product_id_uq' => 'UNIQUE KEY `product_id_uq` (`product_id`)',
        ]],
        // 团队游团期
        'product_group'=>[[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'product_id'=>          ['type' => 'ref*  |','text' => ['产品', 'ship']],
            'dep_date'=>            ['type'=> ' *|date','text'=>['出发日期','dep_date']],
            'min_duoren_price'=>    ['type'=> '     |varchar(255)','text'=>['三/四最低价','min price']],
            'min_price'=>           ['type'=> '     |decimal(16,2)','text'=>['一/二最低价','min price']],
            'create_at'=>           ['type' => 'stamp1|', 'text' => ['创建时间', 'create_at']],
            'order_nums'=>          ['type'=>  '    |int(11)','text'=>['销量','order number']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'product_idx'=>      'KEY `product_idx` (`product_id`)' ,
            'create_at_idx'=>      'KEY `create_at_idx` (`create_at`)' ,
        ]],

        // 团期价格
        'group_fee_detail'=>[[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'group_id'=>            ['type' => 'ref*|','text' => ['团', 'group']],
            'room_type'=>           ['type' => '|varchar(255)','text'=>['房型','fee type']],
            'location'=>            ['type'=>'|varchar(255)','text'=>['位置','location']],
            'price'=>               ['type'=>'    |decimal(16,2)','text'=>['1/2单价','price']], 
            'duoren_price'=>        ['type'=>'    |varchar(255)','text'=>['多人价','duoren price']],          
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'group_idx'=>      'KEY `group_idx` (`group_id`)' 
        ]],

        'pd_dep_city'=>[[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'city_id'=>             ['type' => 'ref*|','text' => ['城市', 'city']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'city_idx'=>      'KEY `city_idx` (`city_id`)' 
        ]],

        'pd_des_city'=>[[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'city_id'=>             ['type' => 'ref*|','text' => ['城市', 'city']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)',
            'city_idx'=>      'KEY `city_idx` (`city_id`)' 
        ]],

        'user_comment' => [[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'name'=>                ['type'=>  '    |varchar(255)','text'=>['姓名','name']],
            'phone'=>               ['type'=>  '    |varchar(255)','text'=>['电话','mobile']],
            'weChat'=>              ['type'=>  '    |varchar(255)','text'=>['微信','wechat']],
            'mail'=>                ['type'=>  '    |varchar(255)','text'=>['mail','mail']],
            'comment'=>             ['type'=>  '    |text','text'=>['留言','comment']],
            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ], [
            'pk' => 'PRIMARY KEY (`id`)'
        ]],

        'order'=>[[
            'id' =>                 ['type' => 'id  |', 'text' => ['主键', 'identity']],
            'name'=>                ['type'=>  '    |varchar(255)','text'=>['姓名','name']],
            'phone'=>               ['type'=>  '    |varchar(255)','text'=>['电话','mobile']],
            'group_id'=>            ['type' => 'ref*|','text' => ['团', 'group']],
            'fee_id'=>              ['type' => 'ref*|','text' => ['费用', 'fee']],
            // 当时系统价
            'dep_date'=>            ['type'=> ' |date','text'=>['出发日期','dep_date']],

            'room_type'=>           ['type' => '|varchar(255)','text'=>['房型','fee type']],
            'location'=>            ['type'=>'|varchar(255)','text'=>['位置','location']],
            'price'=>               ['type'=>'    |decimal(16,2)','text'=>['1/2单价','price']], 
            'duoren_price'=>        ['type'=>'    |varchar(255)','text'=>['多人价','duoren price']],
            
            // 游客所见价
            'use_dep_date'=>            ['type'=> ' |date','text'=>['出发日期','dep_date']],
          
            'use_room_type'=>           ['type' => '|varchar(255)','text'=>['房型','fee type']],
            'use_price'=>               ['type'=>'    |decimal(16,2)','text'=>['1/2单价','price']], 
            'use_duoren_price'=>        ['type'=>'    |varchar(255)','text'=>['多人价','duoren price']],

            'last_update' =>        ['type' => 'stamp|', 'text' => ['最后更新', 'last update']],
        ],[
            'pk' => 'PRIMARY KEY (`id`)'
        ]]
    ];
}
