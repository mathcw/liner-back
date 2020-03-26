<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DB {

    public static $tables = [];
    public static $views = [];

    public static function init(){
    	foreach (['Sys','Business'
				  ,'Org','Product'] as $name) {
			require_once 'table/'.$name.'Table.php';
			$cat = $name.'Table';
		    foreach ($cat::$c as $table => $cfg) {
				if(isset(DB::$tables[$table])){
					sys_error($table.'-表重复');
				}

				DB::$tables[$table] = $cfg;
		    }
		}

    	foreach (['Product'] as $name) {
			require_once 'view/'.$name.'View.php';
			$cat = $name.'View';
		    foreach ($cat::$c as $view => $cfg) {
				if(isset(DB::$views[$view])){
					sys_error($view.'-视图重复');
				}

				DB::$views[$view] = $cfg;
		    }
		}
    }
}

DB::init();

//*:       必填
//id:      禁止写；默认类型：int(11) NOT NULL AUTO_INCREMENT
//ref*:      必填；默认类型：int(11) NOT NULL
//ref:     非必填；默认类型：int(11) NOT NULL
//self:  自动赋值；默认类型：int(11) NOT NULL
//stamp:   禁止写；默认类型：timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//stamp1:  禁止写；默认类型：timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
//gtz:    必须大于0
function tu_get_column_type($tag, $type){
	switch ($tag) {
		case 'id':
			$type = 'int(11) NOT NULL AUTO_INCREMENT';
			break;
		case 'ref':
		case 'ref*':
		case 'self':
        case 'selfEmp':
        case 'selfSup':
        case 'selfRet':
			$type = 'int(11) NOT NULL';
			break;
		case 'stamp':
			$type = 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
			break;
		case 'stamp1':
			$type = 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP';
			break;
		default:
			$pos = strpos($type, ' ');
			if($pos){
				$type = substr($type,0,$pos).' NOT NULL'.substr($type,$pos);
			}else{
				$type .= ' NOT NULL';
			}
			break;
	}
	return $type;
}

function tu_create_table($table,$meta){
	$fields = $meta[0];
	$keys = $meta[1];
	$sql = "CREATE TABLE `$table` (\n";
	foreach ($fields as $field => $cfg) {
		$cfg = explode('|',$cfg['type']);
		$tag = trim($cfg[0]);
		$type = trim($cfg[1]);
		$type = tu_get_column_type($tag,$type);
		$sql .= "`$field` ".$type.",\n";
	}
	foreach ($keys as $key) {
		$sql .= $key.",\n";
	}
	$sql = rtrim($sql,",\n");
	$sql .= "\n)ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	return $sql;
}

function tu_migrate_index($sql,$table,$key_name,$key_cfg){
	if($key_name == 'pk'){
        if(!strpos($sql,'PRIMARY KEY')){
            return "ALTER TABLE `$table` ADD $key_cfg;";
        }
        if(!stripos($sql,$key_cfg)){
            return "ALTER TABLE `$table` DROP PRIMARY KEY , ADD $key_cfg;";
        }
        return;
	}

	if(!strpos($sql,$key_name)){
		return "ALTER TABLE `$table` ADD $key_cfg;";
	}

	if(!stripos($sql,$key_cfg)){
		return "ALTER TABLE `$table` DROP KEY `$key_name` , ADD $key_cfg;";
	}

}

function tu_migrate_column($sql,$table,$field,$type,$pre){
	
	if(!strpos($sql,"`$field` ")){
		return "ALTER TABLE `$table` ADD COLUMN `$field` $type ".($pre?"AFTER `$pre`":"FIRST").";";
	}
	$check = "`$field` ".$type.",";
	if(!stripos($sql,$check)){
		return "ALTER TABLE `$table` CHANGE COLUMN `$field` `$field` $type;";
	}
}

function tu_migrate_table($table,$meta){
	if(!T::$U->db->table_exists($table)){
		$sql = tu_create_table($table,$meta);
		tu_migrate_query($sql);
		return;
	}
	$sql = T::$U->db->query("SHOW CREATE TABLE `$table`")->row_array()["Create Table"];

	$fields = $meta[0];
	$keys = $meta[1];

	$pre = NULL;
	foreach ($fields as $field => $cfg) {
		$cfg = explode('|',$cfg['type']);
		$tag = trim($cfg[0]);
		$type = trim($cfg[1]);
		$type = tu_get_column_type($tag,$type);
		$op = tu_migrate_column($sql,$table,$field,$type,$pre);
		if(!empty($op)){
			tu_migrate_query($op);
		}
		$pre = $field;
	}

	foreach ($keys as $key_name => $key_cfg) {
		$op = tu_migrate_index($sql,$table,$key_name,$key_cfg);
		if(!empty($op)){
			tu_migrate_query($op);
		}
	}
}

function tu_migrate_view($view,$meta){
	$sql =<<< EOF
CREATE OR REPLACE
ALGORITHM=UNDEFINED 
DEFINER=`tlinkerp`@`%` 
SQL SECURITY INVOKER 
VIEW `$view` AS 
EOF;
	$root = $meta['root'];
	preg_match_all("/\S+/",$root,$match);
	$arr = $match[0];
	$root_alias = $arr[count($arr)-1];
    if(count($arr) > 1){
        $root = $arr[0];
    }

	$from = "\nfrom \n\t";
	foreach ($arr as $v) {
		$from .= "`$v` ";
	}

	$select = "\nselect \n\t";
	foreach ($meta['select'] as $field) {
		preg_match_all("/\S+/",$field,$match);
		$arr = $match[0];
		$select .= "`$root_alias`.";
		foreach ($arr as $v) {
            if($v == '*'){
                $select .= "$v ";
            }else{
                $select .= "`$v` ";
            }
		}
		$select .= ', ';
	}

	if(!empty($meta['select_fun'])){
		foreach ($meta['select_fun'] as $field) {
			$select .= $field.', ';
		}
	}

    if(!empty($meta['select_exclude'])){
        $fields = T::$U->db->list_fields($root);

        foreach ($fields as $field) {
            if(in_array($field,$meta['select_exclude'])){
                continue;
            }
            $select .= "`$root_alias`.`$field`, ";
        }
    }

	foreach ($meta['join'] as $table => $cfg) {
		$type = empty($cfg['type']) ? 'left' : $cfg['type'];

		$from .= "\n$type join \n\t";

		preg_match_all("/\S+/",$table,$match);
		$arr = $match[0];
		$table_alias = $arr[count($arr)-1];
        if(count($arr) > 1){
            $table = $arr[0];
        }

		foreach ($arr as $v) {
			$from .= "`$v` ";
		}

		$from .= "on ";

		$and = '';
		foreach ($cfg['cond'] as $left => $right) {
			$left = str_replace('.','`.`',$left);
			$from .= "$and`$left`=`$table_alias`.`$right` ";
			$and = 'and ';
		}

        if(!empty($cfg['other_cond'])){
            foreach ($cfg['other_cond'] as $left => $right) {
                $left = str_replace('.','`.`',$left);
                $from .= "$and`$left`=$right ";
                $and = 'and ';
            } 
        }

		if(!empty($cfg['select'])){
			foreach ($cfg['select'] as $field) {
				preg_match_all("/\S+/",$field,$match);
				$arr = $match[0];
				$select .= "`$table_alias`.";
				foreach ($arr as $v) {
                    if($v == '*'){
                        $select .= "$v ";
                    }else{
                        $select .= "`$v` ";
                    }
				}
				$select .= ', ';
			}
		}

		if(!empty($cfg['select_fun'])){
			foreach ($cfg['select_fun'] as $field) {
				$select .= $field.', ';
			}
		}

        if(!empty($cfg['select_exclude'])){
            $fields = T::$U->db->list_fields($table);

            foreach ($fields as $field) {
                if(in_array($field,$cfg['select_exclude'])){
                    continue;
                }
                $select .= "`$table_alias`.`$field`, ";
            }
        }

	}

	$group_by = '';
	if(!empty($meta['group_by'])){
		$group_by .= "\ngroup by \n\t";
		foreach ($meta['group_by'] as $v) {
			$group_by .= '`'.str_replace('.','`.`',$v).'`';
		}
	} 

	$select = rtrim($select, ', ');
	$sql .= $select.' '.$from.' '.$group_by.';';
	tu_migrate_query($sql);
}

function tu_migrate_query($sql){
	$log = "-- ".date('Y-m-d H:i:s')."\n".$sql."\n\n";
	echo $log;
	file_put_contents('migrate.txt',$log, FILE_APPEND);
	file_put_contents('migrate_log.txt',$log, FILE_APPEND);
	T::$U->db->query($sql);
}

function tu_migrate_db(){
	file_put_contents('migrate.txt','');

	if(!T::$U->db->table_exists('db_migrate')){
		$sql = tu_create_table('db_migrate',[[
            'table' => ['type'=>'*|varchar(32)'],
            'md5' =>   ['type'=>'*|varchar(32)'],
        ],[
            'table_uq' => 'UNIQUE KEY `table_uq` (`table`)'
        ]]);

		tu_migrate_query($sql);
	}
	$q = T::$U->db->get('db_migrate')->result_array();
	$map = array_column($q,'md5','table');

	foreach (DB::$tables as $table => $meta) {
		$sql = tu_create_table($table,$meta);
		$md5 = md5($sql);
		if(!empty($map[$table]) && $md5 == $map[$table]){
			continue;
		}

		tu_migrate_table($table,$meta);
		T::$U->db->replace('db_migrate',['table'=>$table,'md5'=>$md5]);
	}

	foreach (DB::$views as $view => $meta) {
        //对于 select * 必须比对原表字段有无变化
        $tables = [];
        $pick = false;
        if(!empty($meta['select_exclude'])){
            $pick = true;
        }else if(!empty($meta['select']) && $meta['select'][0] == '*'){
            $pick = true;
        }
        if($pick){
            $tables[] = explode(' ',$meta['root'])[0];
        }
        foreach ($meta['join'] as $k => $v) {
            $pick = false;
            if(!empty($v['select_exclude'])){
                $pick = true;
            }else if(!empty($v['select']) && $v['select'][0] == '*'){
                $pick = true;
            }
            if($pick){
                $tables[] = explode(' ',$k)[0];
            }
        }
        $prototype = [];
        $prototype[$view] = $meta;
        foreach ($tables as $table) {
            $prototype[$table] = T::$U->db->list_fields($table);
        }

		$md5 = md5(json_encode($prototype));
		if(!empty($map[$view]) && $md5 == $map[$view]){
			continue;
		}

		tu_migrate_view($view,$meta);
		T::$U->db->replace('db_migrate',['table'=>$view,'md5'=>$md5]);
	}
}