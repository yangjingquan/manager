<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Search extends Model{

    //添加热词
    public function add($data){
        $data['status'] = 0;
        return Db::table('store_search_words')->insert($data);
    }

    //获取热词信息
    public function getHotWords($bis_id,$limit,$offset){
        if($bis_id != 0){
            $where = [
                'sw.status' => ['neq',-1],
                'sw.bis_id' => $bis_id
            ];
        }else{
            $where = [
                'sw.status' => ['neq',-1]
            ];
        }

        $order = [
            'sw.create_time'  => 'desc'
        ];
        return Db::table('store_search_words')->alias('sw')->field('sw.*,bis.bis_name')
            ->join('store_bis bis','sw.bis_id = bis.id','LEFT')
            ->where($where)
            ->limit($offset,$limit)
            ->order($order)
            ->select();
    }

    //获取热词信息数量
    public function getHotWordsCount($bis_id){
        if($bis_id != 0){
            $where = [
                'status' => ['neq',-1],
                'bis_id' => $bis_id
            ];
        }else{
            $where = [
                'status' => ['neq',-1]
            ];
        }

        return Db::table('store_search_words')->where($where)->count();
    }
}

?>