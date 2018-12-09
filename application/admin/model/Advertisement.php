<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Advertisement extends Model{

    public function getAds($limit,$offset){
        $where = [
            'status'  => 1
        ];

        $order = [
            'listorder'  => 'desc',
            'created_time'  =>  'desc'
        ];

        $res = Db::table('store_ads')
            ->where($where)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $res;
    }

    public function getAdsCount(){
        $where = [
            'status'  => 1
        ];

        $count = Db::table('store_ads')->where($where)->count();

        return $count;
    }

    public function getAdInfoById($id){
        $where = [
            'id'  => $id
        ];

        $res = Db::table('store_ads')->where($where)->find();

        return $res;
    }

}

?>