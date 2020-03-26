<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class ProductAction
{
    public static $c = [
        '新增产品'=>[
            'perm_read'=> ['/productStore/Product/read_for_ship_pic'],
            'perm_submit'=>['/productStore/Ticket/submit','/productStore/Youlun/submit','/productStore/Helun/submit'],
        ],
        '修改产品'=>[
            'perm_read'=> ['/productStore/Ticket/read_modify','/productStore/Youlun/read_modify','/productStore/Helun/read_modify'],
            'perm_submit'=>['/productStore/Ticket/submit','/productStore/Youlun/submit','/productStore/Helun/submit'],
        ],
        '新增团期'=>[
            'submit' =>   ['url'=>'/productStore/Group/submit']
        ],
        '修改班期'=>[
            'read' =>     ['url'=>'/productStore/Group/read_modify'],
            'submit' =>   ['url'=>'/productStore/Group/modify']
        ],
        '删除班期'=>[
            'submit' =>   ['url'=>'/productStore/Group/destroy']
        ]
    ];
}