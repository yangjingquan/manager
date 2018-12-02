<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Activitys extends Model{

    //添加活动
    public function add($data){
        return Db::table('cy_activitys')->insert($data);
    }

    //获取所有活动信息
    public function getActivitys($bis_id,$limit,$offset){
        if($bis_id){
            $data = [
                'act.status'  => ['neq',-1],
                'act.bis_id'   => $bis_id
            ];
        }else{
            $data = [
                'act.status'  => ['neq',-1]
            ];
        }


        $order = [
            'act.update_time'  => 'desc'
        ];

        $result = Db::table('cy_activitys')->alias('act')->field('act.*,bis.bis_name')
            ->join('cy_bis bis','act.bis_id = bis.id','left')
            ->where($data)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $result;
    }

    //获取所有活动数量
    public function getActivitysCount($bis_id){
        if($bis_id){
            $data = [
                'act.status'  => ['neq',-1],
                'act.bis_id'   => $bis_id
            ];
        }else{
            $data = [
                'act.status'  => ['neq',-1]
            ];
        }

        $result = Db::table('cy_activitys')->alias('act')
            ->join('cy_bis bis','act.bis_id = bis.id','left')
            ->where($data)
            ->count();

        return $result;
    }
}

?>