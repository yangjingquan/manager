<?php
namespace app\bis\controller;
use think\Controller;
use think\Db;

class Members extends Base {

    //用户列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $count = model('Members')->getAllMembersCount($bis_id);
        $pages = ceil($count / $limit);
        $res = model('Members')->getAllMembers($bis_id,$limit,$offset);
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
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


}
