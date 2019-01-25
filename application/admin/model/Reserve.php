<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Reserve extends Model{

    //获取桌位类型信息
    public function getAllInfo($bis_id,$limit,$offset){
        if(empty($bis_id)){
            $where = 'table.status = 1';
        }else{
            $where = 'table.bis_id = '.$bis_id.' and table.status = 1';
        }
        $res = Db::table('cy_reserve_table_info')->alias('table')->field('table.*,bis.bis_name')
            ->join('cy_bis bis','table.bis_id = bis.id','left')
            ->where($where)
            ->order('table.updated_at desc')
            ->limit($offset,$limit)
            ->select();
        return $res;
    }

    //获取桌位类型数量
    public function getAllCount($bis_id){
        if(empty($bis_id)){
            $where = 'status = 1';
        }else{
            $where = 'bis_id = '.$bis_id.' and status = 1';
        }
        $res = Db::table('cy_reserve_table_info')
            ->where($where)
            ->count();
        return $res;
    }

    public function add($data){
        $res = Db::table('cy_reserve_table_info')->insertGetId($data);
        return $res;
    }

}

?>