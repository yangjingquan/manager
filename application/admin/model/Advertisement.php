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

    //***************************
    //以下是餐饮部分  广告位   代码
    public function getCatAds($bis_id,$limit,$offset){
        if($bis_id != 0){
            $where = [
                'ads.bis_id'  => $bis_id,
                'ads.status'  => 1
            ];
        }else{
            $where = [
                'ads.status'  => 1
            ];
        }

        $order = [
            'ads.listorder'  => 'desc',
            'ads.created_time'  =>  'desc'
        ];

        $res = Db::table('cy_ads')->alias('ads')->field('ads.*,bis.bis_name')
            ->join('cy_bis bis','bis.id = ads.bis_id','left')
            ->where($where)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $res;
    }

    public function getCatAdsCount($bis_id){
        if($bis_id != 0){
            $where = [
                'ads.bis_id'  => $bis_id,
                'ads.status'  => 1
            ];
        }else{
            $where = [
                'ads.status'  => 1
            ];
        }

        $count = Db::table('cy_ads')->alias('ads')
            ->join('cy_bis bis','bis.id = ads.bis_id','left')
            ->where($where)
            ->count();

        return $count;
    }

    public function getCatAdInfoById($id){
        $where = [
            'id'  => $id
        ];

        $res = Db::table('cy_ads')->where($where)->find();

        return $res;
    }

}

?>