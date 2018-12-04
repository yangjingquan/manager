<?php
namespace app\admin\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Coupon extends Base {
    const PAGE_SIZE = 20;
    //优惠券列表
    public function index(){
        $bis_id = input('get.bis_id',0,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
            $count = model('Coupon')->getCouponsCount($bis_id);

        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Coupon')->getCoupons($bis_id,$limit,$offset);

        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/coupon/index',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_res'  => $bis_res,
            'bis_id'  => $bis_id,
        ]);
    }

    //添加优惠券页面
    public function add(){
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/coupon/add',['bis_res'  => $bis_res]);
    }

    //编辑优惠券
    public function edit(){
        //获取参数
        $id = input('get.id');
        $bis_id = input('get.bis_id',0,'intval');
        //获取该分类信息
        $res = Db::table('cy_coupon')->where('id = '.$id)->find();

        $res['start_time'] = substr($res['start_time'],0,10);
        $res['end_time'] = substr($res['end_time'],0,10);

        return $this->fetch('catering/coupon/edit',[
            'coupon'  => $res
        ]);
    }

    //添加优惠券
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => $param['bis_id'],
            'coupon_name' => $param['coupon_name'],
            'max' => $param['max'],
            'par_value' => $param['par_value'],
            'start_time' => date('Y-m-d H:i:s',strtotime($param['start_time'])),
            'end_time' => date('Y-m-d H:i:s',strtotime($param['end_time'])+86399),
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        $res = model('Coupon')->add($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }


    //更改状态
    public function updateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('cy_coupon')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改优惠券
    public function updateCoupon(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        $param = input('post.');
        //设置添加到数据库的数据
        $data = [
            'coupon_name' => $param['coupon_name'],
            'max' => $param['max'],
            'par_value' => $param['par_value'],
            'start_time' => date('Y-m-d H:i:s',strtotime($param['start_time'])),
            'end_time' => date('Y-m-d H:i:s',strtotime($param['end_time'])+86399),
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_coupon')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }
}
