<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MSG extends SHARD{

	public static $redis_shard = [
        '1000000'=>'1',
        '2000000'=>'2',   
	];

	public static $redis_gp = [
        '1'=>['127.0.0.1',6379,0],
        '2'=>['127.0.0.1',6379,0],       
    ];

	public static $db_shard = [
        '1000000'=>'3',
        '2000000'=>'4',      
    ];

    public static $db_gp = [
    	//MSGMB
    	'2' => [
			// 'hostname' => '',
			// 'username' => '',
			// 'password' => '',
			// 'database' => '',
    	],
    	'3' => [
			// 'hostname' => '',
			// 'username' => '',
			// 'password' => '',
			// 'database' => '',
    	],
    	'4' => [
			// 'hostname' => '',
			// 'username' => '',
			// 'password' => '',
			// 'database' => '',
    	],
    	//MSGGP
    	'100' => [
			// 'hostname' => '',
			// 'username' => '',
			// 'password' => '',
			// 'database' => '',
    	],
    	'101' => [
			// 'hostname' => '',
			// 'username' => '',
			// 'password' => '',
			// 'database' => '',
    	],
    	'102' => [
			// 'hostname' => '',
			// 'username' => '',
			// 'password' => '',
			// 'database' => '',
    	],
    ];
}


class MSGGP extends MSG{
	
	public static $db_shard = [
        // '1000000'=>'101',
        // '2000000'=>'102',   
        '1000000'=>'3',
        '2000000'=>'4',
	];
}

