<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 */
class SysAction
{
    public static $c = [
        '修改流程' => [
            'read' => ['url' => '/comm/FlowList/read_step', 'data' => ['schema_name']],
            'submit' => ['url' => '/comm/FlowList/submit', 'data' => ['流程编辑', '流程']],
        ],
        '新增分支' => [
            'read' => ['url' => '/comm/FlowList/read_branch', 'data' => ['schema_name']],
            'submit' => ['url' => '/comm/FlowList/submit_branch', 'data' => ['分支定义编辑', '流程编辑']],
        ],
        '修改分支' => [
            'read' => ['url' => '/comm/FlowList/modify_branch', 'data' => ['schema_name']],
            'submit' => ['url' => '/comm/FlowList/submit_branch', 'data' => ['分支定义编辑', '流程编辑']],
        ],
        '启停分支' => [
            'submit' => ['url' => '/comm/FlowList/toggle/state', 'data' => ['schema_name', 'state']],
        ],
        '启停流程' => [
            'submit' => ['url' => '/comm/FlowList/toggle/state', 'data' => ['schema_name', 'state']],
        ],
        '新增api' => [
            'submit' => ['url' => '/comm/Api/submit'],
        ],
        '修改api' => [
            'submit' => ['url' => '/comm/Api/submit'],
        ],
    ];
}
