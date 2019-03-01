<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Table extends Model{

    //添加桌位
    public function add($data){
        $data['status'] = 1;
        return Db::table('cy_tables')->insert($data);
    }

    //获取所有桌位信息
    public function getTables($bis_id,$limit,$offset){
        if($bis_id){
            $data = [
                't.status'  => ['neq',-1],
                't.bis_id'   => $bis_id
            ];
        }else{
            $data = [
                't.status'  => ['neq',-1],
            ];
        }

        $order = [
            't.id'  => 'desc'
        ];

        $result = Db::table('cy_tables')->alias('t')->field('t.*,bis.bis_name')
            ->join('cy_bis bis','t.bis_id = bis.id','left')
            ->where($data)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $result;
    }

    //获取所有桌位数量
    public function getTablesCount($bis_id){
        if($bis_id){
            $data = [
                't.status'  => ['neq',-1],
                't.bis_id'   => $bis_id
            ];
        }else{
            $data = [
                't.status'  => ['neq',-1],
            ];
        }

        $result = Db::table('cy_tables')->alias('t')
            ->join('cy_bis bis','t.bis_id = bis.id','left')
            ->where($data)
            ->count();

        return $result;
    }
}

?>