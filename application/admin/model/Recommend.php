<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Recommend extends Model{

    //添加推荐位
    public function add($data){
        $res = Db::table('store_recommend')->insert($data);
        return $res;
    }

    //添加平台推荐位
    public function home_add($data){
        $res = Db::table('store_banners')->insert($data);
        return $res;
    }

    //获取所有推荐位信息
    public function getAllRecommends($bis_id,$limit,$offset){
        if($bis_id != 0){
            $where = [
                'rec.status'  => ['neq','-1'],
                'rec.bis_id'  => $bis_id
            ];
        }else{
            $where = [
                'rec.status'  => ['neq','-1']
            ];
        }

        $listorder = [
            'rec.listorder'  => 'desc',
            'rec.id'  => 'desc'
        ];

        $res = Db::table('store_recommend')->alias('rec')->field('rec.*,bis.bis_name')
            ->join('store_bis bis','rec.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset,$limit)
            ->select();
        return $res;
    }

    //获取所有推荐位信息
    public function getAllHomeRecommends($limit,$offset){
        $where = [
            'rec.status'  => ['neq','-1']
        ];

        $listorder = [
            'rec.listorder'  => 'desc',
            'rec.id'  => 'desc'
        ];

        $res = Db::table('store_banners')->alias('rec')->field('rec.*')
            ->where($where)
            ->order($listorder)
            ->limit($offset,$limit)
            ->select();
        return $res;
    }

    //获取所有推荐位总数
    public function getAllRecommendsCount($bis_id){
        if($bis_id != 0){
            $where = [
                'status'  => ['neq','-1'],
                'bis_id'  => $bis_id
            ];
        }else{
            $where = [
                'status'  => ['neq','-1']
            ];
        }

        $res = Db::table('store_recommend')->where($where)->count();
        return $res;
    }

    //获取平台所有推荐位总数
    public function getAllHomeRecommendsCount(){
        $where = [
            'status'  => ['neq','-1']
        ];

        $res = Db::table('store_recommend')->where($where)->count();
        return $res;
    }

    //根据id获取推荐位信息
    public function getRecInfoById($id){
        $where = [
            'id'  => $id
        ];

        $res = Db::table('store_recommend')->where($where)->find();
        return $res;
    }

    //根据id获取平台推荐位信息
    public function getHomeRecInfoById($id){
        $where = [
            'id'  => $id
        ];

        $res = Db::table('store_banners')->where($where)->find();
        return $res;
    }

}

?>