<?php
namespace app\common\model;
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
        $where = [
            'status' => ['neq',-1],
            'bis_id'  => $bis_id
        ];

        $order = [
            'create_time'  => 'desc'
        ];
        return Db::table('store_search_words')->where($where)->limit($offset,$limit)->order($order)->select();
    }

    //获取热词信息数量
    public function getHotWordsCount($bis_id){
        $where = [
            'status' => ['neq',-1],
            'bis_id'  => $bis_id
        ];
        return Db::table('store_search_words')->where($where)->count();
    }
}

?>