<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Products extends Model{

    //添加商品
    public function add($data){
        $res = Db::table('store_products')->insertGetId($data);

        return $res;
    }

    //查询商品
    public function getAllProducts($bis_id,$limit, $offset, $date_from, $date_to,$pro_name){
        $where = " is_jf_product = 0 and status <> -1 and bis_id = ".$bis_id;

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and create_time < '$new_date_to'";
        }
        if($pro_name){
            $where .= " and p_name like '%$pro_name%'";
        }

        $listorder = [
            'id'  => 'desc'
        ];
        $res = Db::table('store_products')->where($where)->order($listorder)->limit($offset,$limit)->select();

        //设置返回数据
        $index = 0;
        $result = array();
        foreach ($res as $item) {
            $result[$index]['id'] = $item['id'];
            $result[$index]['p_name'] = $item['p_name'];
            $result[$index]['inventory'] = $this->getInventoryCount($item['id']);
            $result[$index]['original_price'] = $item['original_price'];
            $result[$index]['associator_price'] = $item['associator_price'];
            $result[$index]['rec_rate'] = $item['rec_rate'] * 100 .'%';
            $result[$index]['on_sale'] = $item['on_sale'];
            $result[$index]['status'] = $item['status'];
            $result[$index]['is_recommend'] = $item['is_recommend'];
            $result[$index]['is_copied'] = empty($item['supply_pro_id']) ? '0' : 1;

            $index ++;
        }
        return $result;
    }

    //查询商品数量
    public function getAllProductCount($bis_id,$date_from = '',$date_to = '',$pro_name = ''){
        $where = " is_jf_product = 0 and status <> -1 and bis_id = ".$bis_id;

        if($date_from){
            $where .= " and create_time >= '$date_from'";
        }
        if($date_to){
            $where .= " and create_time < '$date_to'";
        }
        if($pro_name){
            $where .= " and p_name like '%$pro_name%'";
        }

        $res = Db::table('store_products')->where($where)->count();
        return $res;
    }

    //查询积分商品
    public function getAllJfProducts($bis_id,$limit, $offset, $date_from, $date_to,$pro_name){
        $where = " is_jf_product = 1 and status <> -1 and bis_id = ".$bis_id;

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and create_time < '$new_date_to'";
        }
        if($pro_name){
            $where .= " and p_name like '%$pro_name%'";
        }

        $listorder = [
            'id'  => 'desc'
        ];
        $res = Db::table('store_products')->where($where)->order($listorder)->limit($offset,$limit)->select();

        //设置返回数据
        $index = 0;
        $result = array();
        foreach ($res as $item) {
            $result[$index]['id'] = $item['id'];
            $result[$index]['p_name'] = $item['p_name'];
            $result[$index]['inventory'] = $this->getInventoryCount($item['id']);
            $result[$index]['original_price'] = $item['original_price'];
            $result[$index]['associator_price'] = $item['associator_price'];
            $result[$index]['ex_jifen'] = floor($item['ex_jifen']);
            $result[$index]['on_sale'] = $item['on_sale'];
            $result[$index]['status'] = $item['status'];
            $result[$index]['is_recommend'] = $item['is_recommend'];
            $result[$index]['is_copied'] = empty($item['supply_pro_id']) ? '0' : 1;

            $index ++;
        }
        return $result;
    }

    //查询积分商品数量
    public function getAllJfProductCount($bis_id,$date_from,$date_to,$pro_name){
        $where = " is_jf_product = 1 and status <> -1 and bis_id = ".$bis_id;

        if($date_from){
            $where .= " and create_time >= '$date_from'";
        }
        if($date_to){
            $where .= " and create_time < '$date_to'";
        }
        if($pro_name){
            $where .= " and p_name like '%$pro_name%'";
        }

        $res = Db::table('store_products')->where($where)->count();
        return $res;
    }

    //通过id获取商品信息
    public function getProInfoById($id){
        //设置查询条件
        $where = [
            'pro.id'  => $id
        ];
        $res = Db::table('store_products')->alias('pro')->field('pro.id as pro_id,pro.p_name,pro.rec_rate,pro.pintuan_count,pro.jifen,pro.pintuan_price,pro.cat1_id,pro.cat2_id,pro.cat3_id,pro.defined_cat1_id,pro.defined_cat2_id,pro.brand,pro.unit,pro.producing_area,pro.ex_jifen,pro.original_price,pro.associator_discount,pro.associator_price,pro.vip_discount,pro.vip_price,pro.vvip_discount,pro.vvip_price,weight,pro.listorder,pro.huohao,pro.rate,pro.keywords,pro.introduce,pro.wx_introduce,img.image,img.thumb,img.config_image1,img.config_image2,img.config_image3,img.config_image4,img.wx_config_image1,img.wx_config_image2,img.wx_config_image3,img.wx_config_image4,img.wx_config_image5,img.wx_config_image6,img.wx_config_image7,img.wx_config_image8,img.wx_config_image9,img.wx_config_image10')
            ->join('store_pro_images img','pro.id = img.p_id','LEFT')
            ->where($where)
            ->find();
        $res['rec_rate'] = $res['rec_rate'] * 100;
        return $res;
    }

    //查询库存
    public function getInventoryCount($pro_id){
        $res = Db::table('store_pro_config')->where('pro_id = '.$pro_id.' and status = 1')->SUM('inventory');
        return $res;
    }

    //查询配置信息
    public function getConfigInfo($pro_id){
        $res = Db::table('store_pro_config')->where('pro_id = '.$pro_id.' and status = 1')->select();
        return $res;
    }

    //查询商品库存列表
    public function getProductsInventoryList($bis_id,$limit){
        if($bis_id){
            $where = "pro.is_jf_product = 0 and pro.status <> -1 and pro.bis_id = ".$bis_id;
        }else{
            $where = "pro.is_jf_product = 0 and pro.status <> -1";
        }

        $listorder = [
            'pro.inventory'  => 'desc',
            'pro.id'  => 'desc',
        ];
        $res = Db::table('store_products')->alias('pro')->field('pro.id,pro.p_name,pro.original_price,pro.associator_price,bis.bis_name,pro.inventory')
            ->join('store_bis bis','pro.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($limit)
            ->select();

        return $res;
    }

    //查询商品销量列表
    public function getProductsSoldList($bis_id,$limit){
        if($bis_id){
            $where = "pro.is_jf_product = 0 and pro.status <> -1 and pro.bis_id = ".$bis_id;
        }else{
            $where = "pro.is_jf_product = 0 and pro.status <> -1";
        }

        $listorder = [
            'pro.sold'  => 'desc',
            'pro.id'  => 'desc',
        ];
        $res = Db::table('store_products')->alias('pro')->field('pro.id,pro.p_name,pro.original_price,pro.associator_price,bis.bis_name,pro.sold')
            ->join('store_bis bis','pro.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($limit)
            ->select();

        return $res;
    }
}

?>