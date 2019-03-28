<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class SupplyProducts extends Model{

    //添加商品
    public function add($data){
        $res = Db::table('store_supply_products')->insertGetId($data);

        return $res;
    }

    //查询商品
    public function getAllProducts($limit, $offset, $date_from, $date_to,$pro_name){
        $where = " pro.status <> -1 ";
        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and pro.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and pro.create_time < '$new_date_to'";
        }
        if($pro_name){
            $where .= " and pro.p_name like '%$pro_name%'";
        }

        $listorder = [
            'pro.id'  => 'desc'
        ];
        $res = Db::table('store_supply_products')->alias('pro')->field('pro.id,pro.p_name,pro.original_price,pro.associator_price,pro.rec_rate,pro.on_sale,pro.status,pro.is_recommend,pro.sold,pro.inventory,pro.supply_price')
            ->where($where)
            ->order($listorder)
            ->limit($offset,$limit)
            ->select();

        //设置返回数据
        $index = 0;
        $result = array();
        foreach ($res as $item) {
            $result[$index]['id'] = $item['id'];
            $result[$index]['p_name'] = $item['p_name'];
            $result[$index]['inventory'] = $item['inventory'];
            $result[$index]['original_price'] = $item['original_price'];
            $result[$index]['associator_price'] = $item['associator_price'];
            $result[$index]['rec_rate'] = $item['rec_rate'] * 100 .'%';
            $result[$index]['on_sale'] = $item['on_sale'];
            $result[$index]['status'] = $item['status'];
            $result[$index]['is_recommend'] = $item['is_recommend'];
            $result[$index]['sold'] = $item['sold'];
            $result[$index]['supply_price'] = $item['supply_price'];

            $index ++;
        }
        return $result;
    }

    //查询商品数量
    public function getAllProductCount($date_from = '',$date_to = '',$pro_name = ''){
        $where = " status <> -1";
        if($date_from){
            $where .= " and create_time >= '$date_from'";
        }
        if($date_to){
            $where .= " and create_time < '$date_to'";
        }
        if($pro_name){
            $where .= " and p_name like '%$pro_name%'";
        }

        $res = Db::table('store_supply_products')->where($where)->count();
        return $res;
    }



    //通过id获取商品信息
    public function getProInfoById($id){
        //设置查询条件
        $where = [
            'id'  => $id
        ];
        $res = Db::table('store_supply_products')->where($where)->find();

        $res['pro_id'] = $res['id'];
        $res['rec_rate'] = $res['rec_rate'] * 100;
        $images = json_decode($res['images'],true);
        $res['images'] = $images;
        return $res;
    }
}

?>