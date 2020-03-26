<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket extends TU_Controller {
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
            $base_info['kind'] = PD_KIND_DAN;
            T::$U->db->insert($this->table,$base_info);
            $pd_id = T::$U->db->insert_id();
        }else{
            //update
            T::$U->db->update($this->table,$post['baseInfo'],['id'=>$post['id']]);
            
            $pd_id = $post['id'];
            //delete
            T::$U->db->delete('ticket_itin',['product_id'=>$pd_id]);
            T::$U->db->delete('product_detail',['product_id'=>$pd_id]);

        }
        if(!empty($post['itinInfo'])){
            $itin_info = $post['itinInfo'];
            foreach ($itin_info as $key => &$value) {
                $value['order'] = $key +1;
                $value['product_id'] = $pd_id;
            }
            T::$U->db->insert_batch('ticket_itin',$itin_info);
        }
        if(!empty($post['detailInfo'])){
            $detail = [
                'product_id'=>$pd_id,
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
        $ticket_itin = T::$U->db->get_where('ticket_itin',['product_id'=>$get['id']])->result_array();
        $detail_info = T::$U->db->get_where('product_detail',['product_id'=>$get['id']])->row_array();

        $pic = T::$U->db->select('pic')->get_where('ship_pic',['ship_id'=>$base_info['ship_id']])->result_array();
        $pic = array_column($pic,'pic');
        sys_succeed(null,[
            'baseInfo' => $base_info,
            'ticket_itin'=>$ticket_itin,
            'pic'=>$pic??[],
            'bookInfo'=>$detail_info['book_info']??'',
            'feeInfo'=>$detail_info['fee_info']??'',
            'feeInclude'=>$detail_info['fee_include']??'',
            'feeExclude'=>$detail_info['fee_exclude']??'',
            'cancelInfo'=>$detail_info['cancel_info']??'',
        ]);
    }
}