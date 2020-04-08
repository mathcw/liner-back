<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WebApi extends TU_Controller
{

    public $init_type = INIT_PUB;

    public function init(){
        T::$U->db->join('comm_city','pd_dep_city.city_id=comm_city.id','left');
        $dep_city = T::$U->db->distinct()->select('city_id,name')->get_where('pd_dep_city')->result_array();
        $dep_city = array_column($dep_city,'name','city_id');

        T::$U->db->join('comm_city','pd_des_city.city_id=comm_city.id','left');
        $des_city = T::$U->db->distinct()->select('city_id,name')->get_where('pd_des_city')->result_array();
        $des_city = array_column($des_city,'name','city_id');

        sys_succeed(null, [
            'dep_city' => $dep_city,
            'des_city' => $des_city
        ]);
    }

    public function home()
    {
        // 邮轮公司
        $limit = 3;
        T::$U->db->limit($limit);
        T::$U->db->order_by('ship_num', 'desc');
        $companys = T::$U->db->get_where('cruise_company')->result_array();

        // 目的地 规则 按产品数 取最大6个
        $sql = 'select num,destination from (select count(id) as num, destination from product group by destination) a order by num desc limit 3';
        $rst = T::$U->db->query($sql)->result_array();
        $rst = array_column($rst, 'destination');
        if (!empty($rst)) {
            T::$U->db->where_in('id', $rst);
            $citys = T::$U->db->select('id,name,pic')->get_where('comm_city')->result_array();
        }

        // 航线
        $recommands = [];
        T::$U->db->limit($limit);
        T::$U->db->where('is_recom', 1);
        T::$U->db->join('cruise_ship b', 'b.id = product.ship_id', 'left');
        $pds = T::$U->db->select('product.id,product.name,product.kind,product.ship_id,product.day,product.night,level')->get_where('product')->result_array();
        if (!empty($pds)) {
            $dan = [];
            $you = [];
            $he = [];
            foreach ($pds as $pd) {
                if ($pd['kind'] == PD_KIND_DAN) {
                    $dan[] = $pd;
                }
                if ($pd['kind'] == PD_KIND_YOU) {
                    $you[] = $pd;
                }
                if ($pd['kind'] == PD_KIND_HE) {
                    $he[] = $pd;
                }
            }
            if (!empty($dan)) {
                $ship_ids = array_column($dan, 'ship_id');
                T::$U->db->where_in('id', $ship_ids);
                T::$U->db->group_by('ship_id');
                $pics = T::$U->db->select('ship_id,pic')->get_where('ship_pic')->result_array();
                $pics = array_column($pics, 'pic', 'ship_id');

                foreach ($dan as &$ref) {
                    $ref['pic'] = !empty($pics[$ref['ship_id']]) ? $pics[$ref['ship_id']] : '';
                    T::$U->db->where('product_id', $ref['id']);
                    T::$U->db->select('min(min_duoren_price) as price');
                    $q = T::$U->db->get_where('product_group')->row_array();
                    $ref['price'] = $q['price'];
                }
                $recommands = array_merge($recommands, $dan);
            }
            if (!empty($you)) {
                $product_ids = array_column($you, 'id');
                T::$U->db->where_in('product_id', $product_ids);
                T::$U->db->group_by('product_id');
                $pics = T::$U->db->select('product_id,pic')->get_where('product_pic')->result_array();
                $pics = array_column($pics, 'pic', 'product_id');
                foreach ($you as &$ref) {
                    $ref['pic'] = !empty($pics[$ref['id']]) ? $pics[$ref['id']] : '';
                    T::$U->db->where('product_id', $ref['id']);
                    T::$U->db->select('min(min_duoren_price) as price');
                    $q = T::$U->db->get_where('product_group')->row_array();
                    $ref['price'] = $q['price'];
                }
                $recommands = array_merge($recommands, $you);

            }
            if (!empty($he)) {
                $product_ids = array_column($he, 'id');
                T::$U->db->where_in('product_id', $product_ids);
                T::$U->db->group_by('product_id');
                $pics = T::$U->db->select('product_id,pic')->get_where('product_pic')->result_array();
                $pics = array_column($pics, 'pic', 'product_id');

                foreach ($he as &$ref) {
                    $ref['pic'] = !empty($pics[$ref['id']]) ? $pics[$ref['id']] : '';
                    T::$U->db->where('product_id', $ref['id']);
                    T::$U->db->select('min(min_duoren_price) as price');
                    $q = T::$U->db->get_where('product_group')->row_array();
                    $ref['price'] = $q['price'];
                }
                $recommands = array_merge($recommands, $he);

            }
        }

        sys_succeed(null, [
            '邮轮公司' => $companys,
            '目的地' => $citys,
            '推荐航线' => $recommands,
        ]);
    }

    private function list_read($table, $search, $allow_cond)
    {
        $search = empty($search) ? [] : $search;
        $limit = empty($search['limit']) ? 100 : $search['limit'];
        $start = empty($search['start']) ? 0 : $search['start'];
        $order_field = empty($search['order_field']) ? 'last_update' : $search['order_field'];
        $order_dir = empty($search['order_dir']) ? 'asc' : $search['order_dir'];

        $not_where_cond = ['limit', 'start', 'order_field', 'order_dir'];
        foreach ($search as $field => $value) {
            if (in_array($field, $not_where_cond)) {
                continue;
            }
            if (!in_array($field, $not_where_cond) && !in_array($field, array_keys($allow_cond))) {
                return array(0, []);
            }
            if ($search[$field] !== '' && $search[$field] !== null) {
                if ($allow_cond[$field]['type'] == 'Trim' ) {
                    T::$U->db->like($field, trim($search[$field]));
                }

                if ($allow_cond[$field]['type'] == 'Enum' ) {
                    T::$U->db->where($field, $search[$field]);
                }
                
                if ($allow_cond[$field]['type'] == 'Array' && is_array($search[$field]) && count($search[$field]) != 0){
                    T::$U->db->where_in($field, $search[$field]);
                }
                if ($allow_cond[$field]['type'] == 'From') {
                    T::$U->db->where($allow_cond[$field]['field'] . '>=', $search[$field]);
                }
                if ($allow_cond[$field]['type'] == 'To') {
                    T::$U->db->where($allow_cond[$field]['field'] , '<=', $search[$field]);
                }
            }
        }
        $sql = T::$U->db->get_compiled_select($table, false);
        $total = T::$U->db->count_all_results('', false);

        T::$U->db->order_by($order_field, $order_dir);
        $items = T::$U->db->get('', $limit, $start)->result_array();

        return array($total, $items);
    }

    public function ticket()
    {
        $post = T::$U->post;
        $table = 'product_group_view';
        $allow_cond = [
            'dep_city_id' => ['type'=>'Array'], 
            'destination' => ['type'=>'Array'], 
            'cruise_company_id' => ['type'=>'Array'], 
            'kind' => ['type'=>'Array'], 
            'dep_date_from' => ['type'=>'From','field'=>'dep_date'], 
            'dep_date_to' =>  ['type'=>'TO','field'=>'dep_date'],
        ]; 

        list($total, $items) = $this->list_read($table, $post, $allow_cond);
        sys_succeed(null, [
            'total' => $total,
            'data' => $items,
        ]);
    }

    public function company(){
        $post = T::$U->post;
        $table = 'cruise_company';
        list($total, $items) = $this->list_read($table, $post, []);
        sys_succeed(null, [
            'total' => $total,
            'data' => $items,
        ]);
    }

    public function comment(){
        $post =  T::$U->post;
        if(!empty($post)){
            T::$U->db->insert('user_comment',$post);
        }
        sys_succeed(null);
    }

    public function bourn(){
        $post = T::$U->post;
        $table = 'pd_des_city';

        T::$U->db->select('city_id,name,pic');
        T::$U->db->join('comm_city','pd_des_city.city_id=comm_city.id','left');
        list($total, $items) = $this->list_read($table, $post, [
            'name' => ['type'=>'Trim'], 
        ]);
        sys_succeed(null, [
            'total' => $total,
            'data' => $items,
        ]);
    }
}
