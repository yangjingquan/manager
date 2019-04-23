<?php
namespace app\admin\controller;
use think\Controller;
use think\Exception;
use think\Validate;
use think\Db;

class Recharge extends Base {

    const PAGE_SIZE = 20;
    const WXPAY = 1;
    const ALIPAY = 2;
    const BIS_STORE_TYPE = 1;

    //商城会员余额充值/消费明细
    public function mall_balance_detail(){
        $bis_id = input('get.bis_id',0,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //余额明细
        $res = Model('Recharge')->getBalanceDetail($bis_id,$limit,$offset);
        $count = Model('Recharge')->getBalanceCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        //获取店铺信息
        $bisRes = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('balance_detail',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  => $count,
            'bis_res'  => $bisRes,
            'bis_id'  => $bis_id
        ]);
    }

    //商城会员余额充值/消费明细
    public function catering_balance_detail(){
        $bis_id = input('get.bis_id',0,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //余额明细
        $res = Model('Recharge')->getCatBalanceDetail($bis_id,$limit,$offset);
        $count = Model('Recharge')->getCatBalanceCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        //获取店铺信息
        $bisRes = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/recharge/balance_detail',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  => $count,
            'bis_res'  => $bisRes,
            'bis_id'  => $bis_id
        ]);
    }
}
