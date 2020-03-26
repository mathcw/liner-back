<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CruiseShip extends TU_Controller {
    public $table = 'cruise_ship';

    public function read_convert(&$items){
        $ids = array_column($items,'id');
        if(!empty($ids)){
            T::$U->db->where_in('ship_id',$ids);
            $pics = T::$U->db->select('ship_id,pic')->get_where('ship_pic')->result_array(); 

            $map = array_column($pics,'pic','ship_id');
            foreach($items as &$item){
                $item['list_pic'] = !empty($map[$item['id']])?$map[$item['id']]:'';
            }
        }
    }

    public function submit(){
        $post = T::$U->post;
        if(empty($post['baseInfo'])){
            sys_error('请填写船只信息');
        }
        T::$U->db->trans_start();
        if(empty($post['id'])){
            //create
            T::$U->db->insert($this->table,$post['baseInfo']);
            $ship_id = T::$U->db->insert_id();
        }else{
            //update
            T::$U->db->update($this->table,$post['baseInfo'],['id'=>$post['id']]);
            
            $ship_id = $post['id'];
            //delete
            T::$U->db->delete('ship_des',['ship_id'=>$ship_id]);
            T::$U->db->delete('ship_pic',['ship_id'=>$ship_id]);
            $room_ids = T::$U->db->select('id')->get_where('ship_room',['ship_id'=>$ship_id])->result_array();
            if(!empty($room_ids)){
                T::$U->db->where('ship_id',$ship_id);
                T::$U->db->delete('ship_room');
                T::$U->db->where_in('room_id',array_column($room_ids,'id'));
                T::$U->db->delete('ship_room_pic');
            }
            $food_ids = T::$U->db->select('id')->get_where('ship_food',['ship_id'=>$ship_id])->result_array();
            if(!empty($food_ids)){
                T::$U->db->where('ship_id',$ship_id);
                T::$U->db->delete('ship_food');
                T::$U->db->where_in('food_id',array_column($food_ids,'id'));
                T::$U->db->delete('ship_food_pic');
            }
            $game_ids = T::$U->db->select('id')->get_where('ship_game',['ship_id'=>$ship_id])->result_array();
            if(!empty($game_ids)){
                T::$U->db->where('ship_id',$ship_id);
                T::$U->db->delete('ship_game');
                T::$U->db->where_in('game_id',array_column($game_ids,'id'));
                T::$U->db->delete('ship_game_pic');
            }
            $layout_ids = T::$U->db->select('id')->get_where('ship_layout',['ship_id'=>$ship_id])->result_array();
            if(!empty($layout_ids)){
                T::$U->db->where('ship_id',$ship_id);
                T::$U->db->delete('ship_layout');
                T::$U->db->where_in('layout_id',array_column($layout_ids,'id'));
                T::$U->db->delete('ship_layout_pic');
            }
        }
        
        $des = $post['des'];
        $des['ship_id'] = $ship_id ;
        T::$U->db->insert('ship_des',$des);
        $pic_arr = [];
        if(!empty($post['pic_arr'])){
            foreach($post['pic_arr'] as $pic){
                $pic_arr[] = [
                    'ship_id' => $ship_id,
                    'pic' =>$pic 
                ];
            }
            T::$U->db->insert_batch('ship_pic',$pic_arr);
        }
        
        if(!empty($post['roomInfo'])){
            $roomInfo = $post['roomInfo'];
            foreach ($roomInfo as $room) {
                $pic_arr = $room['pic_arr']??[];
                T::$U->db->insert('ship_room',[
                    'ship_id'=> $ship_id,
                    'room_area'=>$room['room_area'] ??'',
                    'num_of_people'=>$room['num_of_people']??'',
                    'room_type'=>$room['room_type'] ?? 0,
                    'room_kind'=>$room['room_kind']?? 0 ,
                    'floor'=>$room['floor']??'',
                    'des'=>$room['des']??''
                ]);
                $room_id = T::$U->db->insert_id();
                $arr = [];
                foreach ($pic_arr as $pic) {
                    $arr[] = [
                        'room_id' => $room_id,
                        'pic' =>$pic 
                    ];
                }
                if(!empty($arr))
                    T::$U->db->insert_batch('ship_room_pic',$arr);
            }
        }
        if(!empty($post['foodInfo'])){
            $foodInfo = $post['foodInfo'];
            foreach ($foodInfo as $food) {
                $pic_arr = $food['pic_arr']??[];
                T::$U->db->insert('ship_food',[
                    'ship_id'=> $ship_id,
                    'restaurant'=>$food['restaurant']??'',
                    'des'=>$food['des']??''
                ]);
                $food_id = T::$U->db->insert_id();
                $arr = [];
                foreach ($pic_arr as $pic) {
                    $arr[] = [
                        'food_id' => $food_id,
                        'pic' =>$pic 
                    ];
                }
                if(!empty($arr))
                    T::$U->db->insert_batch('ship_food_pic',$arr);
            }
        }
        if(!empty($post['gameInfo'])){
            $gameInfo = $post['gameInfo'];
            foreach ($gameInfo as $game) {
                $pic_arr = $game['pic_arr']??[];
                T::$U->db->insert('ship_game',[
                    'ship_id'=> $ship_id,
                    'name'=>$game['name']??'',
                    'des'=>$game['des']??''
                ]);
                $game_id = T::$U->db->insert_id();
                $arr = [];
                foreach ($pic_arr as $pic) {
                    $arr[] = [
                        'game_id' => $game_id,
                        'pic' =>$pic 
                    ];
                }
                if(!empty($arr))
                    T::$U->db->insert_batch('ship_game_pic',$arr);
            }
        }
        if(!empty($post['shipInfo'])){
            $shipInfo = $post['shipInfo'];
            foreach ($shipInfo as $layout) {
                $pic_arr = $layout['pic_arr']??[];
                T::$U->db->insert('ship_layout',[
                    'ship_id'=> $ship_id,
                    'floor'=>$layout['floor']??0,
                ]);
                $layout_id = T::$U->db->insert_id();
                $arr = [];
                foreach ($pic_arr as $pic) {
                    $arr[] = [
                        'layout_id' => $layout_id,
                        'pic' =>$pic 
                    ];
                }
                if(!empty($arr))
                    T::$U->db->insert_batch('ship_layout_pic',$arr);
            }
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
        $des = T::$U->db->get_where('ship_des',['ship_id'=>$ship_id])->row_array();
        $pic_arr = T::$U->db->get_where('ship_pic',['ship_id'=>$ship_id])->result_array();
        $base_pic_arr = array_column($pic_arr??[],'pic');
        $rooms = T::$U->db->get_where('ship_room',['ship_id'=>$ship_id])->result_array();
        foreach ($rooms as &$room) {
            $room_pic_arr = T::$U->db->select('pic')->get_where('ship_room_pic',['room_id'=>$room['id']])->result_array();
            $room['pic_arr'] = array_column($room_pic_arr,'pic');
        }
        $foods = T::$U->db->get_where('ship_food',['ship_id'=>$ship_id])->result_array();
        foreach ($foods as &$food) {
            $food_pic_arr = T::$U->db->select('pic')->get_where('ship_food_pic',['food_id'=>$food['id']])->result_array();
            $food['pic_arr'] = array_column($food_pic_arr,'pic');
        }
        $games = T::$U->db->get_where('ship_game',['ship_id'=>$ship_id])->result_array();
        foreach ($games as &$game) {
            $game_pic_arr = T::$U->db->select('pic')->get_where('ship_game_pic',['game_id'=>$game['id']])->result_array();
            $game['pic_arr'] = array_column($game_pic_arr,'pic');
        }
        $layouts = T::$U->db->get_where('ship_layout',['ship_id'=>$ship_id])->result_array();
        foreach ($layouts as &$layout) {
            $layout_pic_arr = T::$U->db->select('pic')->get_where('ship_layout_pic',['layout_id'=>$layout['id']])->result_array();
            $layout['pic_arr'] = array_column($layout_pic_arr,'pic');
        }

        sys_succeed(null,[
            'baseInfo' => $base_info,
            'video'=>$base_info['video']??'',
            'des' =>$des['des']??'',
            'des_html' =>$des['des_html']??'',
            'pic_arr'=>$base_pic_arr,
            'roomInfo'=>$rooms,
            'foodInfo'=>$foods,
            'gameInfo'=>$games,
            'layoutInfo'=>$layouts
        ]);
    }
}