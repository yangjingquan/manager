<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Orders extends Model{

    //获取所有订单信息
    public function getAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status,$order_from){
        if($bis_id != 0){
            $where = "mo.bis_id = ".$bis_id." and mem.bis_id = ".$bis_id." and  mo.status = 1 and pay.status = 1";
        }else{
            $where = "mo.status = 1 and pay.status = 1";
        }

        if($order_from != 0){
            $where .= " and mo.order_from = $order_from ";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }
        $listorder = [
            'mo.create_time'  => 'desc'
        ];

        $res = Db::table('store_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mode.post_mode,mem.nickname,mo.rec_name,pay.payment,mo.express_no,mo.order_status,bis.bis_name,mo.order_from')
            ->join('store_payment pay','mo.payment = pay.id','LEFT')
            ->join('store_post_mode mode','mo.mode = mode.id','LEFT')
            ->join('store_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->join('store_bis bis','mo.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();
        return $res;
    }

    //获取所有订单数量
    public function getAllOrdersCount($bis_id,$date_from,$date_to,$order_status,$order_from){
        if($bis_id != 0){
            $where = "mo.bis_id = ".$bis_id." and mem.bis_id = ".$bis_id." and  mo.status = 1 and pay.status = 1";
        }else{
            $where = "mo.status = 1 and pay.status = 1";
        }

        if($order_from != 0){
            $where .= " and mo.order_from = $order_from ";
        }

        if($date_from){
            $where .= " and mo.create_time >= '$date_from'";
        }

        if($date_to){
            $where .= " and mo.create_time < '$date_to'";
        }

        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }

        $res = Db::table('store_main_orders')->alias('mo')
            ->join('store_payment pay','mo.payment = pay.id','LEFT')
            ->join('store_post_mode mode','mo.mode = mode.id','LEFT')
            ->join('store_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->where($where)
            ->count();
        return $res;
    }

    //获取所有团购订单信息
    public function getAllGroupOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status,$order_from){
        if($bis_id != 0){
            $where = "mo.bis_id = ".$bis_id." and mem.bis_id = ".$bis_id." and  mo.status = 1 and pay.status = 1";
        }else{
            $where = "mo.status = 1 and pay.status = 1";
        }

        if($order_from != 0){
            $where .= " and mo.order_from = $order_from ";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }
        $listorder = [
            'mo.create_time'  => 'desc'
        ];

        $res = Db::table('store_group_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mode.post_mode,mem.nickname,mo.rec_name,mo.express_no,mo.order_status,mo.group_num,mo.order_type,bis.bis_name,mo.order_from')
            ->join('store_payment pay','mo.payment = pay.id','LEFT')
            ->join('store_post_mode mode','mo.mode = mode.id','LEFT')
            ->join('store_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->join('store_bis bis','mo.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();
        $index = 0;
        foreach($res as $val){
            $res[$index]['order_type'] = $val['order_type'] == 1 ? '普通' : '团购';
            $index ++;
        }
        return $res;
    }

    //获取所有拼团订单数量
    public function getAllGroupOrdersCount($bis_id,$date_from,$date_to,$order_status,$order_from){
        if($bis_id != 0){
            $where = "mo.bis_id = ".$bis_id." and mem.bis_id = ".$bis_id." and  mo.status = 1 and pay.status = 1";
        }else{
            $where = "mo.status = 1 and pay.status = 1";
        }

        if($order_from != 0){
            $where .= " and mo.order_from = $order_from ";
        }

        if($date_from){
            $where .= " and mo.create_time >= '$date_from'";
        }
        if($date_to){
            $where .= " and mo.create_time < '$date_to'";
        }
        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }

        $res = Db::table('store_group_main_orders')->alias('mo')
            ->join('store_payment pay','mo.payment = pay.id','LEFT')
            ->join('store_post_mode mode','mo.mode = mode.id','LEFT')
            ->join('store_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->where($where)
            ->count();
        return $res;
    }

    //根据id获取订单详情
    public function getOrderInfoById($id){
        //设置查询条件
        $where = [
            'mo.id'  => $id
        ];

        //查询结果
        $res = Db::table('store_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mo.mode,mode.post_mode,mo.rec_name,mo.address,mo.mobile,pay.payment,mo.express_no,mo.order_status,mo.create_time,mo.delivery_cost,mo.remark,mo.total_amount,SUM(sub.amount) as amount,SUM(sub.rate_amount) as rate_amount,sub.pro_id')
            ->join('store_sub_orders sub','sub.main_id = mo.id','LEFT')
            ->join('store_payment pay','mo.payment = pay.id','LEFT')
            ->join('store_post_mode mode','mo.mode = mode.id','LEFT')
            ->join('store_members mem','mo.mem_id = mem.id','LEFT')
            ->where($where)
            ->find();

        return $res;
    }

    //根据id获取订单详情(拼团版)
    public function getGroupOrderInfoById($id){
        //设置查询条件
        $where = [
            'mo.id'  => $id
        ];

        //查询结果
        $res = Db::table('store_group_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mo.mode,mode.post_mode,mo.rec_name,mo.address,mo.mobile,pay.payment,mo.express_no,mo.order_status,mo.create_time,mo.delivery_cost,mo.remark,mo.total_amount,SUM(sub.amount) as amount,SUM(sub.rate_amount) as rate_amount,sub.pro_id')
            ->join('store_group_sub_orders sub','sub.main_id = mo.id','LEFT')
            ->join('store_payment pay','mo.payment = pay.id','LEFT')
            ->join('store_post_mode mode','mo.mode = mode.id','LEFT')
            ->join('store_members mem','mo.mem_id = mem.id','LEFT')
            ->where($where)
            ->find();
        return $res;
    }

    //根据订单id获取订单下商品信息
    public function getProductInfoById($id){
        //设置查询条件
        $where = [
            'so.main_id'  => $id,
            'so.status' => 1
        ];

        $order = [
            'so.id'   => 'asc'
        ];

        //查询结果
        $res = Db::table('store_sub_orders')->alias('so')->field('so.pro_id,p.p_name,so.count,so.unit_price,p.rate,so.rate_amount,so.amount')
                ->join('store_pro_config con','so.pro_id = con.id','LEFT')
                ->join('store_products p','con.pro_id = p.id','LEFT')
                ->where($where)
                ->order($order)
                ->select();
        return $res;
    }

    //根据订单id获取订单下商品信息(拼团版)
    public function getGroupProductInfoById($id){
        //设置查询条件
        $where = [
            'so.main_id'  => $id,
            'so.status' => 1
        ];

        $order = [
            'so.id'   => 'asc'
        ];

        //查询结果
        $res = Db::table('store_group_sub_orders')->alias('so')->field('so.pro_id,p.p_name,so.count,so.unit_price,p.rate,so.rate_amount,so.amount')
            ->join('store_pro_config con','so.pro_id = con.id','LEFT')
            ->join('store_products p','con.pro_id = p.id','LEFT')
            ->where($where)
            ->order($order)
            ->select();
        return $res;
    }

    //获取操作记录
    public function getOperateInfoById($id){
        //设置查询条件
        $where = [
            'o.order_id'  => $id,
            'o.status' => 1
        ];

        $order = [
            'o.state'   => 'asc'
        ];

        $res = Db::table('store_operation')->alias('o')->field('o.state,o.create_time,bis.username')
                ->join('store_bis_admin_users bis','o.operator = bis.id','LEFT')
                ->where($where)
                ->order($order)
                ->select();

        $i = 0;
        foreach($res as $val){
            $result[$i]['num_id'] = $i + 1;
            switch ($val['state']) {
                case 1:
                    $state = '生成订单';
                    break;
                case 2:
                    $state = '订单已确认，款未到';
                    break;
                case 3:
                    $state = '款已到，正在备货';
                    break;
                case 4:
                    $state = '货已发出';
                    break;
                default:
                    $state = '货已收到，谢谢您的定购';
            }
            $result[$i]['state'] = $state;
            $result[$i]['create_time'] = $val['create_time'];
            $result[$i]['username'] = $val['username'];
            $i ++;
        }
        return $result;
    }

    //获取提现记录
    public function getTxRecords($bis_id,$limit, $offset, $date_from, $date_to,$tx_status){
        if($bis_id != 0){
            $where = "rec.bis_id = ".$bis_id." and rec.status = 1";
        }else{
            $where = "rec.status = 1";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and rec.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and rec.create_time < '$new_date_to'";
        }
        if($tx_status){
            $where .= " and rec.tx_status = '$tx_status'";
        }
        $listorder = [
            'rec.create_time'  => 'desc'
        ];

        $res = Db::table('store_withdraw_records')->alias('rec')->field('rec.id,rec.name,rec.mem_id,rec.amount,rec.create_time,rec.tx_status,bis.bis_name')
            ->join('store_bis bis','rec.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();

        return $res;
    }

    //获取提现记录数量
    public function getTxRecordsCount($bis_id,$date_from,$date_to,$tx_status){
        if($bis_id != 0){
            $where = "rec.bis_id = ".$bis_id." and rec.status = 1";
        }else{
            $where = "rec.status = 1";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and rec.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and rec.create_time < '$new_date_to'";
        }
        if($tx_status){
            $where .= " and rec.tx_status = '$tx_status'";
        }

        $res = Db::table('store_withdraw_records')->alias('rec')
            ->where($where)
            ->count();
        return $res;
    }

    //判断物流状态
    public function getLogisticsStatus($bis_id){
        $res = Db::table('store_bis')->field('logistics_status')->where('id = '.$bis_id)->find();
        $logistics_status = $res['logistics_status'];
        return $logistics_status;
    }


    //*******************************
    //以下是餐饮相关接口

    //获取点餐所有订单信息
    public function getDcAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status){
        if($bis_id){
            $where = "mo.type = 1 and mo.bis_id = ".$bis_id." and  mo.status = 1";
        }else{
            $where = "mo.type = 1 and  mo.status = 1";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }
        $listorder = [
            'mo.create_time'  => 'desc'
        ];

        $res = Db::table('cy_main_orders')->alias('mo')->field('mo.id as order_id,mo.table_id,mo.order_no,mo.order_status,mo.total_amount,mo.remark,mo.create_time,bis.bis_name')
            ->join('cy_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->join('cy_bis bis','mo.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();
        return $res;
    }

    //获取点餐所有订单数量
    public function getDcAllOrdersCount($bis_id,$date_from,$date_to,$order_status){
        if($bis_id){
            $where = "mo.type = 1 and mo.bis_id = ".$bis_id." and  mo.status = 1";
        }else{
            $where = "mo.type = 1 and  mo.status = 1";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }

        $res = Db::table('cy_main_orders')->alias('mo')
            ->join('cy_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->join('cy_bis bis','mo.bis_id = bis.id','LEFT')
            ->where($where)
            ->count();
        return $res;
    }

    //获取外卖所有订单信息
    public function getWmAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status){
        if($bis_id){
            $where = "mo.type = 2 and mo.bis_id = ".$bis_id." and  mo.status = 1";
        }else{
            $where = "mo.type = 2 and  mo.status = 1";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }
        $listorder = [
            'mo.create_time'  => 'desc'
        ];

        $res = Db::table('cy_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mo.order_status,mo.total_amount,mo.create_time,mo.rec_name,mo.mobile,mo.address,bis.bis_name')
            ->join('cy_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->join('cy_bis bis','mo.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();
        return $res;
    }

    //获取外卖所有订单数量
    public function getWmAllOrdersCount($bis_id,$date_from,$date_to,$order_status){
        if($bis_id){
            $where = "mo.type = 2 and mo.bis_id = ".$bis_id." and  mo.status = 1";
        }else{
            $where = "mo.type = 2 and  mo.status = 1";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and mo.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and mo.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }

        $res = Db::table('cy_main_orders')->alias('mo')
            ->join('cy_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->join('cy_bis bis','mo.bis_id = bis.id','LEFT')
            ->where($where)
            ->count();
        return $res;
    }
    //根据id获取点餐订单详情
    public function getDcOrderInfoById($id){
        //设置查询条件
        $where = [
            'mo.id'  => $id
        ];

        //查询结果
        $res = Db::table('cy_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mo.remark,mo.total_amount,SUM(sub.amount) as amount,sub.pro_id,mo.order_status,mo.table_id,mo.create_time')
            ->join('cy_sub_orders sub','sub.main_id = mo.id','LEFT')
            ->join('cy_members mem','mo.mem_id = mem.id','LEFT')
            ->where($where)
            ->find();
        return $res;
    }

    //根据订单id获取点餐订单下商品信息
    public function getDcProductInfoById($id){
        //设置查询条件
        $where = [
            'so.main_id'  => $id,
            'so.status' => 1
        ];

        $order = [
            'so.id'   => 'asc'
        ];

        //查询结果
        $res = Db::table('cy_sub_orders')->alias('so')->field('so.pro_id,p.p_name,so.count,so.unit_price,so.amount')
            ->join('cy_products p','so.pro_id = p.id','LEFT')
            ->where($where)
            ->order($order)
            ->select();
        return $res;
    }
    //根据id获取外卖订单详情
    public function getWmOrderInfoById($id){
        //设置查询条件
        $where = [
            'mo.id'  => $id
        ];

        //查询结果
        $res = Db::table('cy_main_orders')->alias('mo')->field('mo.id as order_id,mo.order_no,mo.remark,mo.total_amount,SUM(sub.amount) as amount,mo.order_status,mo.create_time,mo.rec_name,mo.mobile,mo.address')
            ->join('cy_sub_orders sub','sub.main_id = mo.id','LEFT')
            ->join('cy_members mem','mo.mem_id = mem.id','LEFT')
            ->where($where)
            ->find();
        return $res;
    }

    //根据订单id获取外卖订单下商品信息
    public function getWmProductInfoById($id){
        //设置查询条件
        $where = [
            'so.main_id'  => $id,
            'so.status' => 1
        ];

        $order = [
            'so.id'   => 'asc'
        ];

        //查询结果
        $res = Db::table('cy_sub_orders')->alias('so')->field('so.pro_id,p.p_name,so.count,so.unit_price,so.amount')
            ->join('cy_products p','so.pro_id = p.id','LEFT')
            ->where($where)
            ->order($order)
            ->select();
        return $res;
    }

    //获取预定订单信息
    public function getYdAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status){
        if($bis_id){
            $where = "pre.bis_id = ".$bis_id." and  pre.status = 1";
        }else{
            $where = "pre.status = 1";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and pre.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and pre.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and pre.order_status = '$order_status'";
        }
        $listorder = [
            'pre.create_time'  => 'desc'
        ];

        $res = Db::table('cy_pre_orders')->alias('pre')->field('pre.id as order_id,pre.order_status,pre.date,pre.time,pre.remark,pre.create_time,pre.type,pre.link_man,pre.count,pre.mobile,bis.bis_name')
            ->join('cy_bis bis','pre.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset, $limit)
            ->select();

        $index = 0;
        foreach($res as $val){
            switch ($val['type']){
                case 0 :
                    $res[$index]['type'] = '二十人桌';
                    break;
                case 1 :
                    $res[$index]['type'] = '十六人桌';
                    break;
                case 2 :
                    $res[$index]['type'] = '十二人桌';
                    break;
                case 3 :
                    $res[$index]['type'] = '十人桌';
                    break;
                case 4 :
                    $res[$index]['type'] = '四人桌';
                    break;
                default :
                    $res[$index]['type'] = '二人桌';
                    break;
            }
            $index ++;
        }
        return $res;
    }

    //获取预定订单数量
    public function getYdAllOrdersCount($bis_id,$date_from,$date_to,$order_status){
        if($bis_id){
            $where = "pre.bis_id = ".$bis_id." and  pre.status = 1";
        }else{
            $where = "pre.status = 1";
        }

        if($date_from){
            $new_date_from = $date_from.' 00:00:00';
            $where .= " and pre.create_time >= '$new_date_from'";
        }
        if($date_to){
            $new_date_to = $date_to.' 23:59:59';
            $where .= " and pre.create_time < '$new_date_to'";
        }
        if($order_status){
            $where .= " and pre.order_status = '$order_status'";
        }

        $res = Db::table('cy_pre_orders')->alias('pre')
            ->join('cy_bis bis','pre.bis_id = bis.id','LEFT')
            ->where($where)
            ->count();
        return $res;
    }

    //获取所有订单金额
    public function getAllOrdersStatisticCount($order_no,$bis_id,$date_from,$date_to,$order_status,$order_from){
        $date  = date('Y-m-d 00:00:00');
        $dates  = date("Y-m-d 00:00:00",strtotime("+1 day"));


        if($bis_id != 0){
            $where = "mo.bis_id = ".$bis_id." and  mo.status = 1 and pay.status = 1 and mo.order_status = 6 ";
        }else{
            $where = "mo.status = 1 and pay.status = 1  and mo.order_status = 6" ;
        }

        if($order_from != 0){
            $where .= " and mo.order_from = $order_from ";
        }

        if ($date_to  == null &&  $date_from == null){
            $date_from = substr($date,0,10);
            $date_to = substr($dates,0,10);

            $where .= " and mo.pay_time >= '$date' and mo.pay_time < '$dates'";
        }


        if($date_from){
            $where .= " and mo.pay_time >= '$date_from'";
        }

        if($date_to){
            $where .= " and mo.pay_time < '$date_to'";
        }

        if($order_status){
            $where .= " and mo.order_status = '$order_status'";
        }
        if($order_no){
            $where .= " and mo.order_no = '$order_no'";
        }


        //全部
        $res = Db::table('store_main_orders')->alias('mo')
            ->field('mo.total_amount,mo.id')
            ->join('store_payment pay','mo.payment = pay.id','LEFT')
            ->join('store_post_mode mode','mo.mode = mode.id','LEFT')
            ->join('store_members mem','mo.mem_id = mem.mem_id','LEFT')
            ->where($where)
            ->sum('mo.total_amount');

        $data = [
            'quanbu' => $res,
            'date_to' => $date_to,
            'date_from' => $date_from,
        ];

        return $data;
    }

}

?>