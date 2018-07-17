<?php
namespace app\bis\controller;
use think\Controller;
use app\api\controller\Image;
use think\Validate;
use think\Db;

class Account extends Base {

    //银行账户信息
    public function account_detail(){
        $res = Db::table('store_bis_account')->where("bis_id = ".session('bis_id','','bis')." and status = 1")->find();
        return $this->fetch('',[
            'res'  => $res,
        ]);
    }

    //保存信息
    public function save(){
        //获取数据
        $param = input('post.');
        $id = !empty($param['id']) ? $param['id'] : '';
        if(!$id){
            $res = model('BisAccount')->add($param);
        }else{
            $res = model('BisAccount')->updateAccount($param);
        }
    }

    //账户余额信息
    public function balance(){
        $res = Db::table('store_bis_account')->where("bis_id = ".session('bis_id','','bis')." and status = 1")->find();
        return $this->fetch('',[
            'res'  => $res,
        ]);
    }

    //提现申请页面
    public function tixian_apply(){
        $res = Db::table('store_bis_account')->where("bis_id = ".session('bis_id','','bis')." and status = 1")->find();
        return $this->fetch('',[
            'res'  => $res,
        ]);
    }

    //提现申请操作
    public function apply_tixian(){
        //获取参数
        $id = input('post.id');
        $available_money = input('post.available_money');
        $tixian_account = input('post.tixian_account');
        $tixian_money = input('post.cur_tixian_money');
        $res = model('BisAccount')->apply_tixian($id, $available_money, $tixian_account, $tixian_money);

        if($res){
            return $this->success('申请提现成功!');
        }else{
            return $this->error('申请提现失败!');
        }
    }

    //提现记录
    public function tixian_records(){
        $bis_id = session('bis_id', '', 'bis');
        $where = 'bis_id ='.$bis_id;
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $new_date_from = $date_from.' 00:00:00';
        $new_date_to = $date_to.' 23:59:59';

        $current_page = input('get.current_page',1,'intval');
        if($date_from){
            $where .= " and create_time >= '$new_date_from'";
        }
        if($date_to){
            $where .= " and create_time < '$new_date_to'";
        }

        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $count = Db::table('store_tixian_records')->where($where)->count();
        $pages = ceil($count / $limit);
        $res = Db::table('store_tixian_records')->where($where)->order('create_time desc')->limit($offset,$limit)->select();
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'count'  => $count,
            'current_page'  => $current_page,
        ]);
    }

    //修改状态
    public function updateStatus(){
        //获取参数
        $records_id = input('get.id');
        $tixian_status = input('get.tixian_status');
        $data['tixian_status'] = $tixian_status;

        $res = Db::table('store_tixian_records')->where('id = '.$records_id)->update($data);

        if($res){
            return $this->success('修改提现状态成功!');
        }else{
            return $this->error('修改提现状态失败!');
        }
    }

    //财务明细页面
    public function finance_records(){
        $bis_id = session('bis_id', '', 'bis');
        $type = input('get.type');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('BisAccount')->getFinanceRecordsCount($bis_id,$type);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('BisAccount')->getFinanceRecords($bis_id,$type,$limit,$offset);
        return $this->fetch('',[
            'res'  => $res,
            'type'  => $type,
            'pages'  => $pages,
            'count'  => $count,
            'current_page'  => $current_page,
        ]);
    }

}
