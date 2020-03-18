<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 */
class BusinessMod
{

    public static $c = [
        '邮轮公司' => [
            'type' => 1,
            'read' => ['url' => '/business/CruiseCompany/read'],
            'search' => [
                'name' => ['type' => 'Trim'],
            ],
        ],

        '邮轮设置' => [
            'type' => 1,
            'read' => ['url' => '/business/CruiseShip/read'],
            'search' => [
                'name' => ['type' => 'Trim'],
                'company_name' => ['type' => 'Trim'],
            ],
        ],
        '城市设置' => [
            'type' => 1,
            'read' => ['url' => '/business/City/read'],
            'search' => [
                'country' => ['type' => 'Enum'],
                'name' => ['type' => 'Trim'],
            ],
        ],

        '数据字典' => [
            'type' => 1,
            'read' => ['url' => '/business/CommDct/read'],
            'search' => [
                'name' => ['type' => 'Trim'],
                'type_id' => ['type' => 'Enum'],
            ],
        ],

    ];
}
