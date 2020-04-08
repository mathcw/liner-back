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
                    ,'day','destination','dep_city_id']
                ],
                'cruise_ship c'=>[
                    'cond' => ['b.ship_id'=>'id'],
                    'select' => ['level']
                ]
            ]
        ],
    ];
}
