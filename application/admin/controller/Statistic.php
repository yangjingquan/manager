<?php
/**
 * Created by PhpStorm.
 * User: ls19980819
 * Date: 2019/2/18
 * Time: 13:29
 */

namespace app\admin\controller;


use think\Controller;
use think\Db;

class Statistic extends Controller
{

    //营业分析
    public function index(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $order_status = input('get.order_status','0','intval');
        $bis_id = input('get.bis_id',0,'intval');
        $order_from = input('get.order_from',0,'intval');
        $order_no = input('get.order_no');

        $limit = 10;
        //总数量
        $count = model('Products')->getAllProductCount($bis_id);
        //小计
        $xiaoji = model('Purchaseorder')->getAllPurchaseOrderXiaoJi($bis_id);
        $xiaoji = $xiaoji == null ? 0 : $xiaoji;
        //会员数量
        $membersCount = model('Members')->getAllMembersCount($bis_id);
        //新增会员数量
        $newMembersCount = model('Members')->getNewMembersCount($bis_id,$date_from,$date_to);
        //金额
        $money = model('Orders')->getAllOrdersStatisticCount($order_no,$bis_id,$date_from, $date_to, $order_status,$order_from);
        //全部金额
        $quan = $money['quanbu'] == null ? 0 : $money['quanbu'];

        //商品销量
        $res = model('Products')->getProductsSoldList($bis_id,$limit);
        //商品库存
        $kcRes = model('Products')->getProductsInventoryList($bis_id,$limit);

        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('statistic/index',[
            'res'  => $res,
            'kc_res'  => $kcRes,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
            'order_from'  => $order_from,
            'order_no'  => $order_no,
            'quanbu'  => $quan,
            'count'  => $count,
            'xiaoji'  => $xiaoji,
            'members_count'  => $membersCount,
            'new_members_count'  => $newMembersCount
        ]);
    }


    //营业分析
    public function indexs(){

        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');


        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status','0','intval');
        $bis_id = input('get.bis_id',0,'intval');
        $order_from = input('get.order_from',0,'intval');
        $order_no = input('get.order_no');

        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Products')->getAllCount($bis_id);
        //小计
        $xiaoji = model('Purchaseorder')->getAllPurchaseOrderXiaoJi($bis_id);
        if($xiaoji == null)
        {
            $xiaoji = 0;
        }
        //会员个数
        $hui = model('Members')->getAllMemberHui($bis_id);
        //新增会员个数
        $xinhui = model('Members')->getAllMemberThisDay($bis_id);
        //金额
        $money = model('Orders')->getAllOrdersStatisticCount($order_no,$bis_id,$date_from, $date_to, $order_status,$order_from);
        //全部金额
        $quan = $money['quanbu'] ;
        $xianshang = $money['xianshang'];
        $xianxia = $money['xianxia'];
        $kai = $money['date_from'];
        $jie = $money['date_to'];

        if ($quan == null)
        {
            $quan = 0;
        }
        if ($xianshang == null)
        {
            $xianshang = 0;
        }
        if ($xianxia == null)
        {
            $xianxia = 0;
        }




        //总页码
        $pages = ceil($count / $limit);
        //商品销量
        $res = model('Products')->getAllProductsXiao($bis_id,$limit,$offset);
        //商品库存
        $ress = model('Products')->getAllProductsKuCun($bis_id,$limit,$offset);

        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('index/index',[
            'res'  => $res,
            'ress'  => $ress,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
            'order_from'  => $order_from,
            'order_no'  => $order_no,
            'quanbu'  => $quan,
            'xianshang'  => $xianshang,
            'xianxia'  => $xianxia,
            'count'  => $count,
            'xiaoji'  => $xiaoji,
            'hui'  => $hui,
            'xinhui'  => $xinhui,
            'kai'  => $kai,
            'jie'  => $jie,
        ]);
    }


    //商品统计
    public function index_sp(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');


        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status','0','intval');
        $bis_id = input('get.bis_id',0,'intval');
        $order_from = input('get.order_from',0,'intval');
        $order_no = input('get.order_no');

        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Products')->getAllCount($bis_id);
        //小计
        $xiaoji = model('Purchaseorder')->getAllPurchaseOrderXiaoJi($bis_id);
        if($xiaoji == null)
        {
            $xiaoji = 0;
        }
        //会员个数
        $hui = model('Members')->getAllMemberHui($bis_id);
        //新增会员个数
        $xinhui = model('Members')->getAllMemberThisDay($bis_id);
        //金额
        $money = model('Orders')->getAllOrdersStatisticCount($order_no,$bis_id,$date_from, $date_to, $order_status,$order_from);
        //全部金额
        $quan = $money['quanbu'] ;
        $xianshang = $money['xianshang'];
        $xianxia = $money['xianxia'];
        $kai = $money['date_from'];
        $jie = $money['date_to'];

        if ($quan == null)
        {
            $quan = 0;
        }
        if ($xianshang == null)
        {
            $xianshang = 0;
        }
        if ($xianxia == null)
        {
            $xianxia = 0;
        }




        //总页码
        $pages = ceil($count / $limit);
        //商品销量
        $res = model('Products')->getAllProductSp($bis_id,$limit,$offset);
        //商品库存
        $ress = model('Products')->getAllProductsKuCun($bis_id,$limit,$offset);


        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('statistic/index_sp',[
            'res'  => $res,
            'ress'  => $ress,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
            'order_from'  => $order_from,
            'order_no'  => $order_no,
            'quanbu'  => $quan,
            'xianshang'  => $xianshang,
            'xianxia'  => $xianxia,
            'count'  => $count,
            'xiaoji'  => $xiaoji,
            'hui'  => $hui,
            'xinhui'  => $xinhui,
            'kai'  => $kai,
            'jie'  => $jie,
        ]);
    }



    //会员统计
    public function index_hy(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');


        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status','0','intval');
        $bis_id = input('get.bis_id',0,'intval');
        $order_from = input('get.order_from',0,'intval');
        $order_no = input('get.order_no');

        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Products')->getAllCount($bis_id);
        //小计
        $xiaoji = model('Purchaseorder')->getAllPurchaseOrderXiaoJi($bis_id);
        if($xiaoji == null)
        {
            $xiaoji = 0;
        }
        //会员个数
        $hui = model('Members')->getAllMemberHui($bis_id);
        //新增会员个数
        $xinhui = model('Members')->getAllMemberThisDay($bis_id);
        //金额
        $money = model('Orders')->getAllOrdersStatisticCount($order_no,$bis_id,$date_from, $date_to, $order_status,$order_from);
        //全部金额
        $quan = $money['quanbu'] ;
        $xianshang = $money['xianshang'];
        $xianxia = $money['xianxia'];
//        $kai = $money['date_from'];
//        $jie = $money['date_to'];
        $date  = date('Y-m-d');
        $dates  = date("Y-m-d",strtotime("+1 day"));
        $kai = $date;
        $jie = $dates;
        if ($quan == null)
        {
            $quan = 0;
        }
        if ($xianshang == null)
        {
            $xianshang = 0;
        }
        if ($xianxia == null)
        {
            $xianxia = 0;
        }




        //总页码
        $pages = ceil($hui / $limit);

        //商品销量
        $res = model('Members')->getAllMembersHy($limit,$offset);
        //商品库存
        $ress = model('Products')->getAllProductsKuCun($bis_id,$limit,$offset);


        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('statistic/index_hy',[
            'res'  => $res,
            'ress'  => $ress,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
            'order_from'  => $order_from,
            'order_no'  => $order_no,
            'quanbu'  => $quan,
            'xianshang'  => $xianshang,
            'xianxia'  => $xianxia,
            'count'  => $count,
            'xiaoji'  => $xiaoji,
            'hui'  => $hui,
            'xinhui'  => $xinhui,
            'kai'  => $kai,
            'jie'  => $jie,
        ]);
    }

    //销售统计
    public function index_xs(){
        //获取参数
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');


        $current_page = input('get.current_page',1,'intval');
        $order_status = input('get.order_status','0','intval');
        $bis_id = input('get.bis_id',0,'intval');
        $order_from = input('get.order_from',0,'intval');
        $order_no = input('get.order_no');

        $limit = 10;
        $offset = ($current_page - 1) * $limit;

        //商品信息
        $res = model('Orders')->getAllOrdersXs($bis_id,$limit,$offset);
        //总数量
        $count = model('Orders')->getAllOrdersXsCount($bis_id);
        //小计
        $xiaoji = model('Purchaseorder')->getAllPurchaseOrderXiaoJi($bis_id);
        if($xiaoji == null)
        {
            $xiaoji = 0;
        }
        //会员个数
        $hui = model('Members')->getAllMemberHui($bis_id);
        //新增会员个数
        $xinhui = model('Members')->getAllMemberThisDay($bis_id);
        //金额
        $money = model('Orders')->getAllOrdersStatisticCount($order_no,$bis_id,$date_from, $date_to, $order_status,$order_from);
        //全部金额
        $quan = $money['quanbu'] ;
        $xianshang = $money['xianshang'];
        $xianxia = $money['xianxia'];
//        $kai = $money['date_from'];
//        $jie = $money['date_to'];
        $date  = date('Y-m-d');
        $dates  = date("Y-m-d",strtotime("+1 day"));
        $kai = $date;
        $jie = $dates;
        if ($quan == null)
        {
            $quan = 0;
        }
        if ($xianshang == null)
        {
            $xianshang = 0;
        }
        if ($xianxia == null)
        {
            $xianxia = 0;
        }




        //总页码
        $pages = ceil($count / $limit);


        //商品库存
        $ress = model('Products')->getAllProductsKuCun($bis_id,$limit,$offset);


        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('statistic/index_xs',[
            'res'  => $res,
            'ress'  => $ress,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'order_status'  => $order_status,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
            'order_from'  => $order_from,
            'order_no'  => $order_no,
            'quanbu'  => $quan,
            'xianshang'  => $xianshang,
            'xianxia'  => $xianxia,
            'count'  => $count,
            'xiaoji'  => $xiaoji,
            'hui'  => $hui,
            'xinhui'  => $xinhui,
            'kai'  => $kai,
            'jie'  => $jie,
        ]);
    }


    //选择日期
    public function index_xstime(){

        return $this->fetch('statistic/index_xstime',[]);
    }

    //定制统计

    public function index_tongji(){


        Db::query('TRUNCATE `store_sort`;');

        $current_page = input('get.current_page',1,'intval');
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $bis_id = input('get.bis_id',0,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $count = model('Orders')->getAllOrdersHomeCountDz($bis_id,$date_from,$date_to);
        $pages = ceil($count / $limit);
        $res = model('Orders')->getAllOrdersHomeDztongji($bis_id,$limit,$offset,$date_from,$date_to);




        return $this->fetch('',[
            'bis_res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  => $count,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
        ]);
    }


    //发送定制短信

    public function message()
    {
        $current_page = input('get.current_page',1,'intval');
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $bis_id = input('get.bis_id',0,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $count = model('Orders')->getAllOrdersHomeCountDz($bis_id,$date_from,$date_to);
        $pages = ceil($count / $limit);
        //查询数据
        $res = Db::table('store_sort')->select();




        foreach ($res as $k => $v)
        {
            $res[$k]['bis_name'] = $v['bis_name'];
            $res[$k]['create_time'] = $v['create_time'];
            $res[$k]['stop_time'] = $v['stop_time'];
            $res[$k]['money'] = $v['money'];
            $res[$k]['shu'] = $v['shu'];
            $create_time = substr($v['create_time'],0,10);
            $stop_time = substr($v['stop_time'],0,10);
            $money = $v['money'];
            $shu = $v['shu'];
//            $create_time = date("Y-m-d",strtotime("-1 day"));

            $res[$k]['link_mobile'] = $v['link_mobile'];

            //调用第三方接口
            $smsapi = "http://api.smsbao.com/";
            //短信平台帐号
            $user = "dalaa";
            //短信平台密码
            $pass = md5('yipen2018@');
            //随机生成的验证码
            //获取手机号
            $phone = $res[$k]['link_mobile'];
            //要发送的短信内容
            if ($create_time == $stop_time)
            {
                $content="您于".$create_time."收入".$money."元，订单数".$shu.'【一盆】';

            }else{
                $content="您于".$create_time."到".$stop_time."收入".$money."元，订单数".$shu.'【一盆】';

            }
            //发送短信
            $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);

            file_get_contents($sendurl);
        }

        return $this->fetch('statistic/message',[
            'bis_res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  => $count,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
        ]);
    }


}

