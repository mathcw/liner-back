<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ENTRY extends SHARD{


	public static $redis_shard = [
		'1000000' => '1',
		'2000000' => '2'

	];

	public static $redis_gp = [
		'dev'=>['127.0.0.1',6379,0],
		'default'=>['127.0.0.1',6379,0],
        '1'=>['127.0.0.1',6379,0],
        '2'=>['127.0.0.1',6379,0],       
    ];

	public static $db_shard = [
		'0' => 'default', // no shard
	];

    public static $db_gp = [
    	'dev' => [
			// 'hostname' => '',
			// 'username' => '',
			// 'password' => '',
			'database' => '',
    	],
    	'default' => [
			// 'hostname' => '',
			// 'username' => '',
			// 'password' => '',
			// 'database' => '',
    	],
    ];
}

