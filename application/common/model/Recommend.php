<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Recommend extends Model{

    //添加推荐位
    public function add($data){
        $res = Db::table('store_recommend')->insert($data);
        return $res;
    }

    //获取所有推荐位信息
    public function getAllRecommends($bis_id,$limit,$offset){
        $where = [
            'status'  => ['neq','-1'],
            'bis_id'  => $bis_id
        ];

        $listorder = [
            'listorder'  => 'desc',
            'id'  => 'desc'
        ];

        $res = Db::table('store_recommend')->where($where)->order($listorder)->limit($offset,$limit)->select();
        return $res;
    }

    //获取所有推荐位总数
    public function getAllRecommendsCount($bis_id){
        $where = [
            'status'  => ['neq','-1'],
            'bis_id'  => $bis_id
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

}

?>