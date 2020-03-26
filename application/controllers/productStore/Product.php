<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends TU_Controller {
    public $table = 'product';

    public function read_for_ship_pic(){
        $get = T::$U->get;
        if(empty($get['ship_id'])){
            sys_error(i('MISS.PARAM'));
        }
        $pic = T::$U->db->select('pic')->get_where('ship_pic',['ship_id'=>$get['ship_id']])->result_array();
        sys_succeed(NULL,array_column($pic,'pic'));
    }

    public function read_convert(&$items){
        $ids = array_column($items,'id');
        if(!empty($ids)){
            T::$U->db->where_in('product_id',$ids);
            $pics = T::$U->db->select('product_id,pic')->get_where('product_pic')->result_array(); 

            $map = array_column($pics,'pic','product_id');
            foreach($items as &$item){
                $item['list_pic'] = !empty($map[$item['id']])?$map[$item['id']]:'';
            }
        }
    }
}