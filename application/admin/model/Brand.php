<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Brand extends Model{

    //添加品牌
    public function add($data){
        $data['status'] = 0;
        return Db::table('store_brand')->insert($data);
    }

    //获取品牌信息
    public function getBrands($bis_id,$limit,$offset){
        if($bis_id == 0){
            $where = [
                'b.status' => ['neq',-1]
            ];
        }else{
            $where = [
                'b.status' => ['neq',-1],
                'b.bis_id'  => $bis_id
            ];
        }

        $order = [
            'b.create_time'  => 'desc'
        ];
        return Db::table('store_brand')->alias('b')->field('b.*,bis.bis_name')
            ->join('store_bis bis','b.bis_id = bis.id','LEFT')
            ->where($where)
            ->limit($offset,$limit)
            ->order($order)
            ->select();
    }

    //获取品牌信息
    public function getBrandCount($bis_id){
        if($bis_id == 0){
            $where = [
                'status' => ['neq',-1]
            ];
        }else{
            $where = [
                'status' => ['neq',-1],
                'bis_id'  => $bis_id
            ];
        }


        return Db::table('store_brand')->where($where)->count();
    }

    //获取正常的品牌信息
    public function getNormalBrands($bis_id){
        $where = [
            'bis_id'  => $bis_id,
            'status'  => 1
        ];

        return Db::table('store_brand')->where($where)->select();
    }
}

?>