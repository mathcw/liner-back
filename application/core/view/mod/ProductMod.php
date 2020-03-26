<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 */
class ProductMod
{

    public static $c = [
        '产品管理' => [
            'type'=>1,
            'read' => ['url' => '/productStore/Product/read'],
            'search' => [
                'name' => ['type' => 'Trim'],
                'pd_num'=>['type'=> 'Trim'],
                'cruise_company_id'=>['type'=>'Enum'],
                'ship_id'=>['type'=>'Enum']
            ],
        ],
        '班期管理' => [
            'type'=>1,
            'read' => ['url' => '/productStore/Group/read'],
            'search' => [
                'name' => ['type' => 'Trim'],
                'pd_num'=>['type'=> 'Trim'],
                'min_price'=>['type'=>'TrimId'],
                'cruise_company_id'=>['type'=>'Enum'],
                'ship_id'=>['type'=>'Enum'],
                'dep_date'=>['type'=>'Enum']
            ],
        ],

    ];
}
