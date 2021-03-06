<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends TU_Controller {
    public $table = 'product_group';
    public $view = 'product_group_view';


    public function read_convert(&$items){
        $ids = array_column($items,'product_id');
        if(!empty($ids)){
            T::$U->db->where_in('product_id',$ids);
            $pics = T::$U->db->select('product_id,pic')->get_where('product_pic')->result_array(); 

            $map = array_column($pics,'pic','product_id');
            foreach($items as &$item){
                $item['list_pic'] = !empty($map[$item['product_id']])?$map[$item['product_id']]:'';
            }
        }
    }

    public function submit(){
        $post = T::$U->post;
        if(empty($post['product_id'])){
            sys_error('缺少参数');
        }
        $pd_id = $post['product_id'];
        $groups = T::$U->post['group'];
        T::$U->db->trans_start();
        foreach($groups as $group){
            $rst = [
                'product_id'=>$pd_id,
                'dep_date'=>$group['dep_date']
            ];
            T::$U->db->insert('product_group',$rst);
            $group_id = T::$U->db->insert_id();
            $min = 0;
            $duoren_min = '';
            foreach ($group['price_arr'] as &$value) {
                $value['group_id'] = $group_id;
                if(empty($value['duoren_price'])){
                    if(!isset($value['duoren_price'])){
                        $value['duoren_price'] = '';
                    }
                }
                $min = ($min ==0 || $min >$value['price']) ?$value['price']: $min;
                if(!empty($value['duoren_price']) || $value['duoren_price']===0){
                    $duoren_min = ($duoren_min =='' || $duoren_min >$value['duoren_price']) ?$value['duoren_price']: $duoren_min;
                }
            }
            T::$U->db->update('product_group',['min_price'=>$min,'min_duoren_price'=>$duoren_min],['id'=>$group_id]);
            T::$U->db->insert_batch('group_fee_detail',$group['price_arr']);
        }

        T::$U->db->where('product_id',$pd_id);
        $total = T::$U->db->count_all_results('product_group');

        T::$U->db->update('product',['group_count'=>$total],['id'=>$pd_id]);


        T::$U->db->trans_complete();
        sys_succeed(i('SAVE.SUC'));
    }

    public function read_modify(){
        $get = T::$U->get;
        if(empty($get['id'])){
            sys_error('缺少参数');
        }
        $group = T::$U->db->get_where('product_group',['id'=>$get['id']])->row_array();
        $arr = T::$U->db->get_where('group_fee_detail',['group_id'=>$get['id']])->result_array();

        sys_succeed(null,[
            'dep_date'=>$group['dep_date'],
            'price_arr'=>$arr
        ]);
    }

    public function modify(){
        $post = T::$U->post;
        if(empty($post['id'])){
            sys_error('缺少参数');
        }
        $group_id = $post['id'];
        $group = $post['group'];
        T::$U->db->trans_start();

        T::$U->db->update('product_group',['dep_date'=>$group['dep_date']],['id'=>$group_id]);
        $min = 0;
        $duoren_min = '';
        $update_arr = [];
        $insert_arr = [];
        $id_arrs = [];
        foreach ($group['price_arr'] as $value) {
            if(empty($value['id'])){
                if(empty($value['duoren_price'])){
                    if(!isset($value['duoren_price'])){
                        $value['duoren_price'] = '';
                    }
                }
                $insert_arr[] = [
                    'group_id'=>$group_id,
                    'price'=>$value['price']??0,
                    'duoren_price'=>$value['duoren_price'],
                    'room_type'=>$value['room_type']??'',
                    'location'=>$value['location']??''
                ];
            }else{
                if(empty($value['duoren_price'])){
                    if(!isset($value['duoren_price'])){
                        $value['duoren_price'] = '';
                    }
                }
                $update_arr[] = [
                    'id'=>$value['id'],
                    'group_id'=>$group_id,
                    'duoren_price'=>$value['duoren_price'],
                    'price'=>$value['price']??0,
                    'room_type'=>$value['room_type']??'',
                    'location'=>$value['location']??''
                ];
                $id_arrs[] = $value['id'];
            }
            $min = ($min ==0 || $min >$value['price']) ?$value['price']: $min;
            if(!empty($value['duoren_price']) || $value['duoren_price']===0){
                $duoren_min = ($duoren_min =='' || $duoren_min >$value['duoren_price']) ?$value['duoren_price']: $duoren_min;
            }
        }
        if(empty($id_arrs)){
            T::$U->db->delete('group_fee_detail',['group_id'=>$group_id]);
        }else{
            $rst = T::$U->db->select('id')->get_where('group_fee_detail',['group_id'=>$group_id])->result_array();
            $diff = array_diff(array_column($rst,'id'),$id_arrs);
            if(!empty($diff)){
                T::$U->db->where_in('id',$diff);
                T::$U->db->delete('group_fee_detail',['group_id'=>$group_id]);
            }
        }
        
        if(!empty($insert_arr)){
            T::$U->db->insert_batch('group_fee_detail',$insert_arr);
        }
        if(!empty($update_arr)){
            T::$U->db->update_batch('group_fee_detail',$update_arr,'id');
        }
        T::$U->db->update('product_group',['min_price'=>$min,'min_duoren_price'=>$duoren_min],['id'=>$group_id]);
        T::$U->db->trans_complete();
        sys_succeed(i('SAVE.SUC'));
    }

    public function destroy() {
        $group  = T::$U->db->get_where('product_group',['id'=>T::$U->post['id']])->row_array();
        T::$U->db->trans_start();
        sys_destroy(T::$U->post, $this->table, 'id');
        T::$U->db->delete('group_fee_detail',['group_id'=>T::$U->post['id']]);

        T::$U->db->where('product_id',$group['product_id']);
        $total = T::$U->db->count_all_results('product_group');

        T::$U->db->update('product',['group_count'=>$total],['id'=>$group['product_id']]);
        T::$U->db->trans_complete();
        sys_succeed(i('DEL.SUC'));
    }
}