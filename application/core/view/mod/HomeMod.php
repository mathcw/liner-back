<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *  首页
 */
class HomeMod
{

    public static $c = [
        '管理员首页' => [
            'read' => ['url' => '/Home/Admin/read_home'],
            'perm_submit' => ['/org/Account/set_password', '/org/Account/set_emp_profile_photo'],
            'public' => 1,
        ],
        '供应商首页' => [
            'read' => ['url' => '/Home/Supplier/read_home'],
            'perm_submit' => ['/org/Account/set_password', '/org/Account/set_supp_profile_photo'],
            'public' => 1,
        ],
        '零售商首页' => [
            'read' => ['url' => '/Home/Retailer/read_home'],
            'public' => 1,
        ],
    ];
}
