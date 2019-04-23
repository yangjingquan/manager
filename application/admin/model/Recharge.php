<?php
namespace app\admin\model;
use think\Exception;
use think\Model;
use think\Db;
use app\api\service\CheckService;
class Recharge extends Model{

    //商城会员余额充值/消费明细
    public function getBalanceDetail($bis_id,$limit,$offset){
        if($bis_id != 0){
            $where = "r.bis_id = ".$bis_id." and r.recharge_status = 2 and r.bis_type = 1";
        }else{
            $where = "r.recharge_status = 2 and r.bis_type = 1";
        }
        $res = Db::table('store_member_recharge_records')->alias('r')->field('r.id as r_id,r.amount,r.type,.r.created_at,m.nickname,b.bis_name')
            ->join('store_members m','m.mem_id = r.openid','left')
            ->join('store_bis b','b.id = r.bis_id','left')
            ->where($where)
            ->order('r.created_at desc')
            ->limit($offset,$limit)
            ->select();

        return $res;
    }

    //商城会员余额充值/消费记录数量
    public function getBalanceCount($bis_id){
        if($bis_id != 0){
            $where = "r.bis_id = ".$bis_id." and r.recharge_status = 2 and r.bis_type = 1";
        }else{
            $where = "r.recharge_status = 2 and r.bis_type = 1";
        }
        $res = Db::table('store_member_recharge_records')->alias('r')
            ->join('store_members m','m.mem_id = r.openid','left')
            ->join('store_bis b','b.id = r.bis_id','left')
            ->where($where)
            ->count();

        return $res;
    }

    //餐饮会员余额充值/消费明细
    public function getCatBalanceDetail($bis_id,$limit,$offset){
        if($bis_id != 0){
            $where = "r.bis_id = ".$bis_id." and r.recharge_status = 2 and r.bis_type = 2";
        }else{
            $where = "r.recharge_status = 2 and r.bis_type = 2";
        }
        $res = Db::table('store_member_recharge_records')->alias('r')->field('r.id as r_id,r.amount,r.type,.r.created_at,m.nickname,b.bis_name')
            ->join('cy_members m','m.mem_id = r.openid','left')
            ->join('cy_bis b','b.id = r.bis_id','left')
            ->where($where)
            ->order('r.created_at desc')
            ->limit($offset,$limit)
            ->select();

        return $res;
    }

    //餐饮会员余额充值/消费记录数量
    public function getCatBalanceCount($bis_id){
        if($bis_id != 0){
            $where = "r.bis_id = ".$bis_id." and r.recharge_status = 2 and r.bis_type = 2";
        }else{
            $where = "r.recharge_status = 2 and r.bis_type = 2";
        }
        $res = Db::table('store_member_recharge_records')->alias('r')
            ->join('cy_members m','m.mem_id = r.openid','left')
            ->join('cy_bis b','b.id = r.bis_id','left')
            ->where($where)
            ->count();

        return $res;
    }
}

?>