<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 */
class OrgMod
{

    public static $c = [
        '权限管理' => [
            'type' => 1,
            'read' => ['url' => '/org/Auth/read'],
            'search' => [
                'name' => ['type' => 'Trim'],
            ],
        ],

        '账号管理' => [
            'type' => 1,
            'read' => ['url' => '/org/Account/read'],
            'auth_filter' => [
            ],
            'search' => [
                'name' => ['type' => 'Trim'],
            ],
        ],
    ];
}
