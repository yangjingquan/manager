<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Allproducts extends Model{

    //添加商品
    public function add($data){
        $res = Db::table('store_allproducts')->insertGetId($data);
        return $res;
    }


}

?>