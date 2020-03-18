<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SHARD
{
	public static function gp($id,$type)
	{
		switch ($type) {
			case 'redis':
				$shard = & static::$redis_shard;
				break;
			case 'db':
				$shard = & static::$db_shard;
				break;
			default:
				sys_error('wrong gp type');
				break;
		}
	    foreach ($shard as $max => $gp) {
	        if($id <= $max){
	            break;
	        }
	    }
	    return $gp;
	}

	public static function &redis_gp($gp)
	{
		
		static $res = [];
	    if(empty($res[$gp])){

	    	$cfg = static::$redis_gp[$gp]??null;
	    	if($cfg === null){
	    		sys_error(-1);
	    	}

		    $res[$gp] = new redis();
		    $res[$gp]->connect($cfg[0], $cfg[1]);
		    $res[$gp]->select($cfg[2]);
	    }

	    return $res[$gp];
	}
	
	public static function &db_gp($gp)
	{
		static $res = [];
		static $hold = [];

	    if(empty($res[$gp])){

	    	$cfg = static::$db_gp[$gp]??null;
			
	    	if($cfg === null){
	    		return $hold[0];
	    		// sys_error(-1);
	    	}

	    	$cfg =  set_db_cfg($cfg);
	    	
	        $res[$gp] = T::$U->load->database($cfg,true);
	    }

	    return $res[$gp];
	}

	public static function &redis($id)
	{
		$gp = static::gp($id,'redis');
		return static::redis_gp($gp);
	}

	public static function &db($id)
	{
		$gp = static::gp($id,'db');
		return static::db_gp($gp);
	}

}

function set_db_cfg($cfg)
{
	return array_merge([
		'dsn'	=> '',
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '12345678',
		'database' => MAIN_DB,
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => TRUE
	],$cfg);
}

require_once 'EntryShard.php';
require_once 'MsgShard.php';
require_once 'B2bShard.php';

