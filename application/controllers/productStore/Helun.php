<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Helun extends TU_Controller {
    public $table = 'product';

    public function submit(){
        $post = T::$U->post;
        if(empty($post['baseInfo'])){
            sys_error('请填写产品信息');
        }
        T::$U->db->trans_start();
        if(empty($post['id'])){
            //create
            $base_info = $post['baseInfo'];
            $base_info['kind'] = PD_KIND_HE;
            T::$U->db->insert($this->table,$base_info);
            $pd_id = T::$U->db->insert_id();
        }else{
            //update
            T::$U->db->update($this->table,$post['baseInfo'],['id'=>$post['id']]);
            
            $pd_id = $post['id'];
            //delete
            T::$U->db->delete('product_itin',['product_id'=>$pd_id]);
            T::$U->db->delete('itin_pic',['product_id'=>$pd_id]);
            T::$U->db->delete('product_detail',['product_id'=>$pd_id]);

        }
        $pic_arr = [];
        if(!empty($post['pic_arr'])){
            foreach($post['pic_arr'] as $pic){
                $pic_arr[] = [
                    'product_id' => $pd_id,
                    'pic' =>$pic 
                ];
            }
            T::$U->db->insert_batch('product_pic',$pic_arr);
        }
        if(!empty($post['itinInfo'])){
            $itinInfo = $post['itinInfo'];
            foreach ($itinInfo as $key => $itin) {
                $pic_arr = $itin['pic_arr']??[];
                T::$U->db->insert('product_itin',[
                    'product_id'=> $pd_id,
                    'order'=>$key +1,
                    'des'=>$itin['des']??'',
                    'arr_time'=>$itin['arr_time']??'',
                    'level_time'=>$itin['level_time']??'',
                    'dep_city'=>$itin['dep_city']??'',
                    'destination'=>$itin['destination']??'',
                    'breakfast'=>$itin['breakfast']??'',
                    'lunch'=>$itin['lunch']??'',
                    'dinner'=>$itin['dinner']??'',
                    'accommodation'=>$itin['accommodation']??'',

                ]);
                $itin_id = T::$U->db->insert_id();
                $arr = [];
                foreach ($pic_arr as $pic) {
                    $arr[] = [
                        'itin_id' => $itin_id,
                        'product_id'=>$pd_id,
                        'pic' =>$pic 
                    ];
                }
                if(!empty($arr))
                    T::$U->db->insert_batch('itin_pic',$arr);
            }
        }
        if(!empty($post['detailInfo'])){
            $detail = [
                'product_id'=>$pd_id,
                'bright_spot'=>$post['detailInfo']['brightSpot']??'',
                'book_info'=>$post['detailInfo']['bookInfo']??'',
                'fee_info'=>$post['detailInfo']['feeInfo']??'',
                'fee_include'=>$post['detailInfo']['feeInclude']??'',
                'fee_exclude'=>$post['detailInfo']['feeExclude']??'',
                'cancel_info'=>$post['detailInfo']['cancelInfo']??'',
            ];
            T::$U->db->insert('product_detail',$detail);
        }

        T::$U->db->trans_complete();
        
        sys_succeed(i('SAVE.SUC'));
    }

    public function read_modify(){
        $get = T::$U->get;
        if (empty($get['id'])) {
            sys_error(i('MISS.PARAM'));
        }
        $ship_id = $get['id'];
        $base_info = T::$U->db->get_where($this->table,['id'=>$ship_id])->row_array();
        T::$U->db->order_by('order');
        $itins = T::$U->db->get_where('product_itin',['product_id'=>$get['id']])->result_array();
        foreach( $itins as &$itin){
            $pic_arr = T::$U->db->select('pic')->get_where('itin_pic',['itin_id'=>$itin['id']])->result_array();
            $itin['pic_arr'] = array_column($pic_arr,'pic');
        }
        $detail_info = T::$U->db->get_where('product_detail',['product_id'=>$get['id']])->row_array();

        $pic = T::$U->db->select('pic')->get_where('product_pic',['product_id'=>$get['id']])->result_array();
        $pic = array_column($pic,'pic');
        sys_succeed(null,[
            'baseInfo' => $base_info,
            'itinInfo'=>$itins,
            'pic_arr'=>$pic??[],
            'brightSpot'=>$detail_info['bright_spot']??'',
            'bookInfo'=>$detail_info['book_info']??'',
            'feeInfo'=>$detail_info['fee_info']??'',
            'feeInclude'=>$detail_info['fee_include']??'',
            'feeExclude'=>$detail_info['fee_exclude']??'',
            'cancelInfo'=>$detail_info['cancel_info']??'',
        ]);
    }
}