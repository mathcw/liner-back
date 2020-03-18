<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class I18n
{
	public static $lang = 0;

	public static $c = [
	];
}

require_once 'locales/ZhCN.php';
require_once 'locales/ZhTW.php';
require_once 'locales/English.php';
require_once 'locales/Portug.php';

function i($code,$lang=NULL){

    if($lang === NULL){
        $lang = 'zh-CN';
	}
	$map = [
        'zh-CN'=>'ZhCN',
        'zh-TW'=>'ZhTW',
        'en-US'=>'English',
        'pt-BR'=>'Portug',
    ];
	$lang_kind = $map[$lang];
	
	$c = & $lang_kind ::$c;
	
	if(empty($c)){
		$c = I18n::$c;
	}

	$arr = explode('.',$code);
	$output = '';
	foreach ($arr as $v) {
		if (empty($c[$v])) {
			$output .= $v;
		}else{
			$output .= $c[$v];
		}
	}
	return $output;
}

function i18n_field($table,$field){
	require_once APPPATH.'core/db/DB.php';
	if(is_array(DB::$tables[$table][0][$field]['text'])){
		return I18n::$c[DB::$tables[$table][0][$field]['text'][0]]??DB::$tables[$table][0][$field]['text'][0];
	}
	return I18n::$c[DB::$tables[$table][0][$field]['text']] ?? DB::$tables[$table][0][$field]['text'];
}