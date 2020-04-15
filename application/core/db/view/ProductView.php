<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class ProductView
{
    public static $c = [
        'product_group_view' => [
            'select' => ['*'],
            'root' => 'product_group a',
            'join' => [
                'product b' => [
                    'cond' => ['a.product_id'=>'id'],
                    'select' => ['name','cruise_company_id','ship_id','pd_num','kind','night'
                    ,'day','destination','dep_city_id','theme']
                ],
                'cruise_ship c'=>[
                    'cond' => ['b.ship_id'=>'id'],
                    'select' => ['level']
                ]
            ]
        ],

        'order_view' => [
            'select' => ['*'],
            'root' => 'order a',
            'join' => [
                'product_group b' => [
                    'cond' => ['a.group_id'=>'id'],
                    'select' => ['product_id']
                ],
                'product c'=>[
                    'cond' => ['b.product_id'=>'id'],
                    'select' => ['name pd_name']
                ]
            ]
        ],
    ];
}
