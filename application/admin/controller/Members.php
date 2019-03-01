<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Members extends Base {
    const PAGE_SIZE = 20;
    //用户列表
    public function index(){
        $bis_id = input('get.bis_id',-1,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        $count = model('Members')->getAllMembersCount($bis_id);
        $pages = ceil($count / $limit);
        $res = model('Members')->getAllMembers($bis_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
        ]);
    }

    //详情页面
    public function detail(){
        //获取数据
        $id = input('get.mem_id');
        //设置查询条件
        $where['m.id'] = $id;
        $res = Db::table('store_members')->alias('m')->field('')
                ->join('store_province p','m.province_id = p.id','LEFT')
                ->join('store_city c','m.city_id = c.id','LEFT')
                ->where($where)
                ->find();
        return $this->fetch('',[
            'res'   =>  $res
        ]);
    }

    //餐饮会员列表
    public function cy_index(){
        $bis_id = input('get.bis_id',-1,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        $count = model('Members')->getCatAllMembersCount($bis_id);
        $pages = ceil($count / $limit);
        $res = model('Members')->getCatAllMembers($bis_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/members/index',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
        ]);
    }


}
