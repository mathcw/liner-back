<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 */
class SysMod
{

    public static $c = [
        '业务流程' => [
            'type' => 1,
            'read' => ['url' => '/comm/FlowList/read'],
            'filter' => ['creator' => 2],
            'perm_submit' => ['/comm/Flow/update_flow'],
            'search' => [
                'name' => ['type' => 'Trim'],
            ],
        ],
        'api管理' => [
            'type' => 1,
            'read' => ['url' => '/comm/Api/read'],
            'search' => [
                'name' => ['type' => 'Trim'],
            ],
        ],
        '系统参数设置' => [
            'type' => 1,
            'read' => ['url' => '/sys/Config/read'],
            'submit' => ['url' => '/sys/Config/submit'],
        ],
    ];
}
