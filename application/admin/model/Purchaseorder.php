<?php
/**
 * Created by PhpStorm.
 * User: ls19980819
 * Date: 2019/1/23
 * Time: 13:49
 */

namespace app\admin\model;


use think\Model;
use think\Db;

class Purchaseorder extends Model
{
    //获取所有采购单数量
    public function getAllPurchaseOrderCount($bis_id){

        if($bis_id != 0){
            $where = " status <> -1 and bis_id = ".$bis_id;
        }else{
            $where = " status <> -1";
        }
        $res = Db::table('store_purchase_order')->where($where)->count();
        return $res;
    }

    //获取所有采购单金额
    public function getAllPurchaseOrderXiaoJi($bis_id){

        if($bis_id){
            $where = " status <> -1 and bis_id = ".$bis_id;
            $where = " status = 6 and bis_id = ".$bis_id;
        }else{
            $where = " status <> -1  ";
            $where = " status = 6 ";
        }
        $res = Db::table('store_purchase_order')->where($where)->sum('subtotal');
        return $res;
    }

    //获取所有采购单信息
    public function getAllPurchaseOrder($bis_id,$limit,$offset){
        if($bis_id != 0){
//            $where = " det.status <> -1 and det.bis_id = ".$bis_id;
            $where = " det.bis_id = ".$bis_id;
        }else{
//            $where = " det.status <> -1";
//            $where = " det.bis_id = ".$bis_id;
            $n = null;
                $where = " det.status != '$n'";
        }
        $listorder = [
            'det.id'  => 'desc'
        ];
        $res = Db::table('store_purchase_order')->alias('det')
            ->field('det.*,bis.bis_name,sup.name,pro.p_name')
            ->join('store_bis bis','det.bis_id = bis.id','LEFT')
            ->join('store_all_supplier sup','sup.id = det.supplier_name','LEFT')
            ->join('store_products pro','pro.bar_code = det.product','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset,$limit)
            ->select();


        foreach ($res as $k => $v)
        {
            $res[$k]['id']=$v['id'];
            switch($v['status']){
                case -1:
                    $status_text =  '作废';
                    break;
                case 1:
                    $status_text =  '待审核';
                    break;
                case 2:
                    $status_text =  '待入库';
                    break;
                case 3:
                    $status_text =  '已入库';
                    break;


            }
            $res[$k]['status'] = $status_text;
        }



        return $res;
    }

    //根据id获取商品信息
    public function getAllPurchaseOrderById($id){
        //设置查询条件
        $where = [
            'so.id'  => $id,
        ];

        $order = [
            'so.id'   => 'asc'
        ];

        //查询结果
        $res = Db::table('store_purchase_order')
            ->alias('so')
            ->field('so.*,bis.bis_name,sup.name,pro.p_name')
            ->join('store_bis bis','so.bis_id = bis.id','LEFT')
            ->join('store_all_supplier sup','sup.id = so.supplier_name','LEFT')
            ->join('store_products pro','pro.bar_code = so.product','LEFT')
            ->where($where)
            ->order($order)
            ->find();
        return $res;
    }


}