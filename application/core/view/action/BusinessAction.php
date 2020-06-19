<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class BusinessAction
{
    public static $c = [
        '新增邮轮公司' => [
            'submit'=>['url'=>'/business/CruiseCompany/submit'],
        ],

        '修改邮轮公司' => [
            'read'  =>['url'=>'/business/CruiseCompany/read_modify'],
            'submit'=>['url'=>'/business/CruiseCompany/submit'],
        ],

        '启停邮轮公司' => [
            'submit' => ['url'=>'/business/CruiseCompany/toggle/state','data'=>['id','state']], 
        ],

        '新增邮轮' => [
            'submit'=>['url'=>'/business/CruiseShip/submit'],
        ],

        '修改邮轮' => [
            'read' => ['url'=>'/business/CruiseShip/read_modify'],
            'submit'=>['url'=>'/business/CruiseShip/submit'],
        ],

        '启停邮轮' => [
            'submit' => ['url'=>'/business/CruiseShip/toggle/state','data'=>['id','state']], 
        ],

        '新增城市'=>[
            'submit' =>  ['url'=>'/business/City/submit'],
        ],

        '修改城市'=>[
            'submit' =>  ['url'=>'/business/City/submit'],
        ],

        '启停城市'=>[
            'submit' => ['url'=>'/business/City/toggle/state','data'=>['id','state']],     
        ],

        '新增数据字典'=>[
            'submit' =>  ['url'=>'/business/CommDct/submit'],
        ],

        '修改数据字典' => [
            'submit' =>  ['url'=>'/business/CommDct/submit'],
        ],

        '启停数据字典'=>[
            'submit' => ['url'=>'/business/CommDct/toggle/state','data'=>['id','state']],     
        ],


        '新增轮播图'=>[
            'submit' =>  ['url'=>'/business/Banner/submit'],
        ],

        '修改轮播图' => [
            'submit' =>  ['url'=>'/business/Banner/submit'],
        ],

        '删除轮播图' => [
            'submit' =>  ['url'=>'/business/Banner/destroy','data'=>['id']],
        ],


    ];
}