<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class OrgAction
{
    public static $c = [
        '新增权限'=>[
            'read' =>   ['url'=>'/org/Auth/read_new','data'=>['id']],
            'submit' => ['url'=>'/org/Auth/submit','data'=>'auth'],
        ],
        '编辑权限'=>[
            'read' =>   ['url'=>'/org/Auth/read_modify','data'=>['id']],
            'submit' => ['url'=>'/org/Auth/submit','data'=>'auth'],
        ],

        '新增账号' => [
            'submit' =>  ['url'=>'/org/Account/submit'],
        ],

        '修改账号' => [
            'submit' =>  ['url'=>'/org/Account/submit'],
        ],

        '设置账号权限'=>[
            'read'=>    ['url'=>'/org/Account/read_auth','data'=>['id']],
            'submit' => ['url'=>'/org/Account/set_auth','data'=>['id','auth_id']],
        ],

        '删除账号'=>[
            'submit'=>['url'=>'/org/Account/destroy','data'=>['id']]
        ],

        '启停账号' => [
            'submit' => ['url'=>'/org/Account/toggle/state','data'=>['id','state']],
        ],

        '重置账号密码'=>[
            'submit' => ['url'=>'/org/Account/reset_password'],
        ],
    ];
}