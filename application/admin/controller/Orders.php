<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Exception;
use think\Log;

class Orders extends Base {
    const PAGE_SIZE = 20;
    const STORE_TYPE = 1;
    const CATERING_TYPE = 2;

    //订单列表
    public function index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status','0','intval');
        $bis_id = input('get.bis_id',0,'intval');
        $order_from = input('get.order_from',0,'intval');
        $is_supply_order = 0;

        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getAllOrdersCount($bis_id,$date_from, $date_to, $order_status,$order_from,$is_supply_order);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status,$order_from,$is_supply_order);
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
            'order_from'  => $order_from,
        ]);
    }

    //拼团订单列表
    public function group_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status','0','intval');
        $bis_id = input('get.bis_id',0,'intval');
        $order_from = input('get.order_from',0,'intval');

        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getAllGroupOrdersCount($bis_id,$date_from, $date_to, $order_status,$order_from);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getAllGroupOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status,$order_from);
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
            'order_from'  => $order_from
        ]);
    }

    //订单详情
    public function detail(){
        //获取参数
        $id = input('get.id');
        //获取店铺id
        $bis_res = Db::table('store_main_orders')->field('bis_id')->where('id = '.$id)->find();
        $bis_id = $bis_res['bis_id'];
        //获取运货方式
        $post_res = Db::table('store_post_mode')->where('status = 1')->select();
        //获取订单详情
        $order_info = Model('Orders')->getOrderInfoById($id);
        //获取订单内商品信息
        $sub_order_info = Model('Orders')->getProductInfoById($id);
        //判断物流状态
        $logistics_info = Model('Orders')->getLogisticsStatus($bis_id);

        return $this->fetch('',[
            'post_res'  => $post_res,
            'order_info'   => $order_info,
            'sub_order_info'   => $sub_order_info,
            'logistics_info'   => $logistics_info,
        ]);
    }

    //订单详情(拼团版)
    public function group_detail(){
        //获取参数
        $id = input('get.id');
        //获取店铺id
        $bis_res = Db::table('store_group_main_orders')->field('bis_id')->where('id = '.$id)->find();
        $bis_id = $bis_res['bis_id'];
        //获取运货方式
        $post_res = Db::table('store_post_mode')->where('status = 1')->select();
        //获取订单详情
        $order_info = Model('Orders')->getGroupOrderInfoById($id);
        //获取订单内商品信息
        $sub_order_info = Model('Orders')->getGroupProductInfoById($id);
        //判断物流状态
        $logistics_info = Model('Orders')->getLogisticsStatus($bis_id);

        return $this->fetch('',[
            'post_res'  => $post_res,
            'order_info'   => $order_info,
            'sub_order_info'   => $sub_order_info,
            'logistics_info'   => $logistics_info,
        ]);
    }

    //修改订单状态
    public function changeOrderStatus(){
        //获取参数
        $param = input('post.');

        //获取当前订单信息
        $orderRes = Db::table('store_main_orders')->where('id = '.$param['order_id'])->find();
        $orderStatus = $orderRes['order_status'];
        if(empty($param['order_status'])){
            $param['order_status'] = $orderStatus;
        }

        $data = [
            'order_status' => $param['order_status'],
            'mode' => $param['post_mode'],
            'express_no' => $param['express_no'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('store_main_orders')->where('id = '.$param['order_id'])->update($data);

        if($param['order_status'] == '4' && $res == 1){
            $this->setTemMessage($param['order_id'],'org');
        }

        return $this->success('修改订单状态成功!');
    }

    //修改订单状态(拼团版)
    public function changeGroupOrderStatus(){
        //获取参数
        $param = input('post.');

        //获取当前订单信息
        $orderRes = Db::table('store_group_main_orders')->where('id = '.$param['order_id'])->find();
        $orderStatus = $orderRes['order_status'];
        if(empty($param['order_status'])){
            $param['order_status'] = $orderStatus;
        }

        $data = [
            'order_status' => $param['order_status'],
            'mode' => $param['post_mode'],
            'express_no' => $param['express_no'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('store_group_main_orders')->where('id = '.$param['order_id'])->update($data);

        if($param['order_status'] == '4' && $res == 1){
            $this->setTemMessage($param['order_id'],'group');
        }

        return $this->success('修改订单状态成功!');
    }

    //提现记录
    public function tx_record(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $tx_status = input('get.tx_status',0,'intval');
        $bis_id = input('get.bis_id',0,'intval');

        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getTxRecordsCount($bis_id,$date_from, $date_to, $tx_status);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getTxRecords($bis_id,$limit, $offset, $date_from, $date_to,$tx_status);
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'tx_status'  => $tx_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
        ]);
    }

    //确认提现
    public function confirm_tx_order(){
        //获取参数
        $param = input('get.');
        $mem_id = $param['mem_id'];
        $amount = $param['amount'];

        $data = [
            'tx_status' => 2,
            'update_time'  => date('Y-m-d H:i:s')
        ];
        //更新提现表状态
        $res = Db::table('store_withdraw_records')->where('id = '.$param['id'])->update($data);
        //更新会员表相关字段
        $where = "mem_id = '$mem_id'";
        $txz_res = Db::table('store_members')->field('tixianzhong')->where($where)->find();
        $txz_amount = $txz_res['tixianzhong'];
        $new_txz_amount['tixianzhong'] = $txz_amount - $amount;
        $update_mem_res = Db::table('store_members')->where($where)->update($new_txz_amount);

        return $this->success('提现成功!','Orders/tx_record');
    }

    //取消提现
    public function cancel_tx_order(){
        //获取参数
        $param = input('get.');
        $mem_id = $param['mem_id'];
        $amount = $param['amount'];

        $data = [
            'tx_status' => 3,
            'update_time'  => date('Y-m-d H:i:s')
        ];
        //更新提现表状态
        $res = Db::table('store_withdraw_records')->where('id = '.$param['id'])->update($data);
        //更新会员表相关字段
        $where = "mem_id = '$mem_id'";
        $txz_res = Db::table('store_members')->field('ketixian,tixianzhong')->where($where)->find();
        $ktx_amount = $txz_res['ketixian'];
        $txz_amount = $txz_res['tixianzhong'];
        $new_amount['ketixian'] = $ktx_amount + $amount;
        $new_amount['tixianzhong'] = $txz_amount - $amount;
        $update_mem_res = Db::table('store_members')->where($where)->update($new_amount);
        return $this->success('取消成功!','Orders/tx_record');
    }

    //设置发货时发送模板消息
    public function setTemMessage($order_id,$order_type){
        //获取参数
        $order_info = $this->getOrderInfo($order_id,$order_type);

        $touser = $order_info['mem_id'];
        $template_id = $this->getTemplateId($order_id,$order_type);
        $form_id = $order_info['prepay_id'];
        $appid = $order_info['appid'];
        $secret = $order_info['secret'];
        $page = 'pages/index/index';
        //获取access_token
        $access_token_json = $this->getAccessToken($appid,$secret);
        $arr = json_decode($access_token_json,true);

        $access_token = $arr['access_token'];

        //获取订单信息
        $order_no = $order_info['order_no'];
        $pro_detail = $order_info['pro_detail'];
        $order_time = $order_info['order_time'];
        $now = date('Y-m-d H:i:s');
        $post_mode = $order_info['post_mode'];
        $express_no = $order_info['express_no'];

        //设置请求url
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token;
        //设置参数
        $data = '{"keyword1": {"value": "'.$order_no.'","color": "#0b3768"},
                  "keyword2": {"value": "'.$pro_detail.'","color": "#0b3768"},
                  "keyword3": {"value": "'.$order_time.'","color": "#0b3768"},
                  "keyword4": {"value": "'.$now.'","color": "#0b3768"},
                  "keyword5": {"value": "'.$post_mode.'","color": "#0b3768"},
                  "keyword6": {"value": "'.$express_no.'","color": "#0b3768"}}';

        $post_data='{"touser":"'.$touser.'","template_id":"'.$template_id.'","page":"'.$page.'","form_id":"'.$form_id.'","data":'.$data.'}';

        $result = $this->sendPost($url,$post_data);
        return $result;
    }

    function sendPost($url, $data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;

    }

    //获取订单信息
    public function getOrderInfo($order_id,$order_type){
        if($order_type == 'org'){
            $main_res = Db::table('store_main_orders')->alias('main')->field('main.order_no,main.create_time,main.mem_id,main.appid,main.secret,main.prepay_id,main.express_no,mode.post_mode')
                ->join('store_post_mode mode','main.mode = mode.id','LEFT')
                ->where('main.id = '.$order_id)
                ->find();

            $sub_res = Db::table('store_sub_orders')->alias('sub')->field('pro.p_name')
                ->join('store_pro_config con','sub.pro_id = con.id','LEFT')
                ->join('store_products pro','con.pro_id = pro.id','LEFT')
                ->where('sub.main_id = '.$order_id.' and sub.status = 1')
                ->find();

            $pro_count =  Db::table('store_sub_orders')
                ->where('main_id = '.$order_id.' and status = 1')
                ->count();
        }else{
            $main_res = Db::table('store_group_main_orders')->alias('main')->field('main.order_no,main.create_time,main.mem_id,main.appid,main.secret,main.prepay_id,main.express_no,mode.post_mode')
                ->join('store_post_mode mode','main.mode = mode.id','LEFT')
                ->where('main.id = '.$order_id)
                ->find();

            $sub_res = Db::table('store_group_sub_orders')->alias('sub')->field('pro.p_name')
                ->join('store_pro_config con','sub.pro_id = con.id','LEFT')
                ->join('store_products pro','con.pro_id = pro.id','LEFT')
                ->where('sub.main_id = '.$order_id.' and sub.status = 1')
                ->find();

            $pro_count =  Db::table('store_group_sub_orders')
                ->where('main_id = '.$order_id.' and status = 1')
                ->count();
        }

        if($pro_count > 1){
            $pro_detail = $sub_res['p_name'].'等'.$pro_count.'件';
        }else{
            $pro_detail = $sub_res['p_name'];
        }
        $order_info = [
            'mem_id'  => $main_res['mem_id'],
            'order_no'  => $main_res['order_no'],
            'pro_detail'  => $pro_detail,
            'order_time'  => $main_res['create_time'],
            'post_mode'  => $main_res['post_mode'],
            'express_no'  => $main_res['express_no'],
            'prepay_id'  => $main_res['prepay_id'],
            'appid'  => $main_res['appid'],
            'secret'  => $main_res['secret']
        ];

        return $order_info;
    }

    //获取access_token
    public function getAccessToken($appid,$secret){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    //获取模板id
    public function getTemplateId($order_id,$order_type){
        if($order_type == 'org'){
            //获取bis_id
            $order_res = Db::table('store_main_orders')->field('bis_id')->where('id = '.$order_id)->find();
        }else{
            //获取bis_id
            $order_res = Db::table('store_group_main_orders')->field('bis_id')->where('id = '.$order_id)->find();
        }
        $bis_id = $order_res['bis_id'];
        //获取模板id
        $bis_res = Db::table('store_bis')->field('fahuo_template_id')->where('id = '.$bis_id)->find();
        $template_id = $bis_res['fahuo_template_id'];
        return $template_id;
    }


    //************************
    //以下是餐饮部分

    //点餐订单列表
    public function dc_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status',0,'intval');
        $bis_id = input('get.bis_id',0,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getDcAllOrdersCount($bis_id,$date_from, $date_to, $order_status);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getDcAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/orders/dc_index',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_res'  => $bis_res,
            'bis_id'  => $bis_id
        ]);
    }

    //点餐订单详情
    public function dc_detail(){
        //获取参数
        $id = input('get.id');
        //获取订单详情
        $order_info = Model('Orders')->getDcOrderInfoById($id);
        //获取订单内商品信息
        $sub_order_info = Model('Orders')->getDcProductInfoById($id);

        return $this->fetch('catering/orders/dc_detail',[
            'order_info'   => $order_info,
            'sub_order_info'   => $sub_order_info
        ]);
    }
    //外卖订单列表
    public function wm_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status',0,'intval');
        $bis_id = input('get.bis_id',0,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getWmAllOrdersCount($bis_id,$date_from, $date_to, $order_status);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getWmAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/orders/wm_index',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_res'  => $bis_res,
            'bis_id'  => $bis_id
        ]);
    }

    //外卖订单详情
    public function wm_detail(){
        //获取参数
        $id = input('get.id');
        //获取订单详情
        $order_info = Model('Orders')->getWmOrderInfoById($id);
        //获取订单内商品信息
        $sub_order_info = Model('Orders')->getWmProductInfoById($id);

        return $this->fetch('catering/orders/wm_detail',[
            'order_info'   => $order_info,
            'sub_order_info'   => $sub_order_info
        ]);
    }

    //修改订单状态
    public function changeCatOrderStatus(){
        //获取参数
        $param = input('post.');

        if(empty($param['order_status'])){
            return $this->error('订单状态不能为空','Orders/dc_index');
        }
        $data = [
            'order_status' => $param['order_status'],
            'update_time'  => date('Y-m-d H:i:s')
        ];

        Db::startTrans();
        try{
            //若置状态为已完成，加积分
            if($param['order_status'] == 3 && $param['type'] == 1){
                //获取当前订单信息
                $orderInfo = Model('Orders')->getMainOrderOnly($param['order_id']);
                $openid = $orderInfo['mem_id'];
                $bisId = $orderInfo['bis_id'];
                $totalAmount = $orderInfo['total_amount'];
                $OrderNo = $orderInfo['order_no'];
                $this->addJifen($bisId,$openid,$totalAmount,$OrderNo);
            }
            $res = Db::table('cy_main_orders')->where('id = '.$param['order_id'])->update($data);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
        }

        if($param['type'] == 1){
            return $this->success('修改订单状态成功!','Orders/dc_index');
        }else{
            return $this->success('修改订单状态成功!','Orders/wm_index');
        }
    }

    //预定订单列表
    public function yd_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status',0,'intval');
        $bis_id = input('get.bis_id',0,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getYdAllOrdersCount($bis_id,$date_from, $date_to, $order_status);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getYdAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/orders/yd_index',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res
        ]);
    }

    //确认预定订单
    public function confirm_yd_order(){
        //获取参数
        $param = input('get.');

        $data = [
            'order_status' => 3,
            'update_time'  => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_pre_orders')->where('id = '.$param['order_id'])->update($data);

        return $this->success('修改订单状态成功!','Orders/yd_index');
    }

    //取消预定订单
    public function cancel_yd_order(){
        //获取参数
        $param = input('get.');

        $data = [
            'order_status' => 2,
            'update_time'  => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_pre_orders')->where('id = '.$param['order_id'])->update($data);

        return $this->success('取消订单成功!','Orders/yd_index');
    }

    //商城供货订单列表
    public function mall_supply_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status','0','intval');
        $bis_id = input('get.bis_id',0,'intval');
        $order_from = input('get.order_from',0,'intval');
        $is_supply_order = 1;

        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getAllOrdersCount($bis_id,$date_from, $date_to, $order_status,$order_from,$is_supply_order);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getAllOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status,$order_from,$is_supply_order);
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
            'order_from'  => $order_from,
        ]);
    }

    //商城供货订单详情
    public function mall_supply_detail(){
        //获取参数
        $id = input('get.id');
        //获取店铺id
        $bis_res = Db::table('store_main_orders')->field('bis_id')->where('id = '.$id)->find();
        $bis_id = $bis_res['bis_id'];
        //获取运货方式
        $post_res = Db::table('store_post_mode')->where('status = 1')->select();
        //获取订单详情
        $order_info = Model('Orders')->getOrderInfoById($id);
        //获取订单内商品信息
        $sub_order_info = Model('Orders')->getProductInfoById($id);
        //判断物流状态
        $logistics_info = Model('Orders')->getLogisticsStatus($bis_id);
        return $this->fetch('',[
            'post_res'  => $post_res,
            'order_info'   => $order_info,
            'sub_order_info'   => $sub_order_info,
            'logistics_info'   => $logistics_info,
        ]);
    }

    //商城修改供货订单状态
    public function changeSupplyOrderStatus(){
        //获取参数
        $param = input('post.');
        //获取店铺id
        $bis_res = Db::table('store_main_orders')->field('bis_id')->where('id = '.$param['order_id'])->find();
        $bis_id = $bis_res['bis_id'];
        //获取当前订单信息
        $orderRes = Db::table('store_main_orders')->where('id = '.$param['order_id'])->find();
        $orderStatus = $orderRes['order_status'];
        if(empty($param['order_status'])){
            $param['order_status'] = $orderStatus;
        }
        $data = [
            'order_status' => $param['order_status'],
            'mode' => $param['post_mode'],
            'express_no' => $param['express_no'],
            'update_time'  => date('Y-m-d H:i:s')
        ];

        //开启事务
        Db::startTrans();
        try{
            //获取订单内商品供货总价格
            $supplyProTotalAmount = Model('Orders')->getSupplyProductTotalAmount($param['order_id']);
            //获取店铺的充值剩余金额
            $bisInfo = Model('Bis')->getBisInfo($bis_id);
            $bisBalance = $bisInfo['balance'];

            //如果有订单商品中有供货商品
            if($supplyProTotalAmount > '0.00'){
                if(floatval($bisBalance) > '0.00'){
                    if(floatval($supplyProTotalAmount) > floatval($bisBalance)){
                        throw new Exception('充值余额不足，请立即充值!',-1);
                    }else{
                        $balance = floatval($bisBalance) - floatval($supplyProTotalAmount);
                        //更新余额
                        $bisRes = Model('Bis')->updateBalance($bis_id,$balance);
                        if($bisRes != 1){
                            throw new Exception('余额更新失败!',-1);
                        }
                        //生成扣款记录
                        $deductRes = Model('Deduct')->createDeductRecords($bis_id,$supplyProTotalAmount,$balance,self::STORE_TYPE);
                        if($deductRes != 1){
                            throw new Exception('扣款记录创建失败!',-1);
                        }
                    }
                }else{
                    throw new Exception('充值余额不足，请立即充值!',-1);
                }
            }
            $res = Db::table('store_main_orders')->where('id = '.$param['order_id'])->update($data);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }

        return $this->success('修改订单状态成功!');
    }

    //餐饮供货订单列表
    public function cat_supply_index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status','0','intval');
        $bis_id = input('get.bis_id',0,'intval');
        $order_from = input('get.order_from',0,'intval');
        $is_supply_order = 1;

        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Orders')->getAllCatSupplyOrdersCount($bis_id,$date_from, $date_to, $order_status,$order_from,$is_supply_order);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Orders')->getAllCatSupplyOrders($bis_id,$limit, $offset, $date_from, $date_to,$order_status,$order_from,$is_supply_order);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/orders/cat_supply_index',[
            'res'  => $res,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
            'order_from'  => $order_from,
        ]);
    }

    //餐饮供货订单详情
    public function cat_supply_detail(){
        //获取参数
        $id = input('get.id');
        //获取店铺id
        $bis_res = Db::table('cy_mall_main_orders')->field('bis_id')->where('id = '.$id)->find();
        $bis_id = $bis_res['bis_id'];
        //获取运货方式
        $post_res = Db::table('store_post_mode')->where('status = 1')->select();
        //获取订单详情
        $order_info = Model('Orders')->getCatSupplyOrderInfoById($id);
        //获取订单内商品信息
        $sub_order_info = Model('Orders')->getCatSupplyProductInfoById($id);
        //判断物流状态
        $logistics_info = Model('Orders')->getCatLogisticsStatus($bis_id);

        return $this->fetch('catering/orders/cat_supply_detail',[
            'post_res'  => $post_res,
            'order_info'   => $order_info,
            'sub_order_info'   => $sub_order_info,
            'logistics_info'   => $logistics_info,
        ]);
    }

    //餐饮修改供货订单状态
    public function changeCatSupplyOrderStatus(){
        //获取参数
        $param = input('post.');
        //获取店铺id
        $bis_res = Db::table('cy_mall_main_orders')->field('bis_id')->where('id = '.$param['order_id'])->find();
        $bis_id = $bis_res['bis_id'];

        //获取当前订单信息
        $orderRes = Db::table('cy_mall_main_orders')->where('id = '.$param['order_id'])->find();
        $orderStatus = $orderRes['order_status'];
        if(empty($param['order_status'])){
            $param['order_status'] = $orderStatus;
        }
        $data = [
            'order_status' => $param['order_status'],
            'mode' => $param['post_mode'],
            'express_no' => $param['express_no'],
            'update_time'  => date('Y-m-d H:i:s')
        ];

        //开启事务
        Db::startTrans();
        try{
            //获取订单内商品供货总价格
            $supplyProTotalAmount = Model('Orders')->getCatSupplyProductTotalAmount($param['order_id']);
            //获取店铺的充值剩余金额
            $bisInfo = Model('Bis')->getCatBisInfo($bis_id);
            $bisBalance = $bisInfo['balance'];

            //如果有订单商品中有供货商品
            if($supplyProTotalAmount > '0.00'){
                if(floatval($bisBalance) > '0.00'){
                    if(floatval($supplyProTotalAmount) > floatval($bisBalance)){
                        throw new Exception('充值余额不足，请立即充值!',-1);
                    }else{
                        $balance = floatval($bisBalance) - floatval($supplyProTotalAmount);
                        //更新余额
                        $bisRes = Model('Bis')->updateCatBalance($bis_id,$balance);
                        if($bisRes != 1){
                            throw new Exception('余额更新失败!',-1);
                        }
                        //生成扣款记录
                        $deductRes = Model('Deduct')->createDeductRecords($bis_id,$supplyProTotalAmount,$balance,self::CATERING_TYPE);
                        if($deductRes != 1){
                            throw new Exception('扣款记录创建失败!',-1);
                        }

                    }
                }else{
                    throw new Exception('充值余额不足，请立即充值!',-1);
                }
            }
            $res = Db::table('cy_mall_main_orders')->where('id = '.$param['order_id'])->update($data);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }

        return $this->success('修改订单状态成功!');
    }

    //会员增加积分(餐饮点餐 已付款to完成)
    public function addJifen($bisId,$openid,$total_amount,$orderNo){
        //获取积分比例
        $bisInfo = Db::table('cy_bis')->where('id = '.$bisId)->find();
        $jifen_ratio = $bisInfo['jifen_ratio'];
        if($jifen_ratio > 0){
            //可获得积分
            $jifen = floor($total_amount / $jifen_ratio);
            if($jifen > 0){
                //更新会员积分
                Db::table('cy_members')->where("mem_id = '$openid'")->setInc('jifen',$jifen);
                //生成积分明细
                $this->createJifenDetail($openid,$jifen,$orderNo);
            }
        }

        return true;
    }

    //生成积分明细
    public function createJifenDetail($openid,$jifen,$order_no){
        //生成积分明细记录
        $jf_data = [
            'mem_id'  => $openid,
            'changed_jifen'  => $jifen,
            'type'  => 1,
            'remark'  => $order_no,
            'create_time'  => date('Y-m-d H:i:s'),
        ];
        Db::table('cy_jifen_detailed')->insert($jf_data);
    }


}
