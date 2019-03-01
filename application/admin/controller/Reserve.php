<?php
namespace app\admin\controller;
use think\Controller;
use app\api\controller\Image;
use think\Db;

class Reserve extends Base {
    const PAGE_SIZE = 20;

    //桌位类型信息
    public function index(){
        $bis_id = input('get.bis_id','');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Reserve')->getAllCount($bis_id);

        //总页码
        $pages = ceil($count / $limit);
        //获取类型信息
        $table_res = model('Reserve')->getAllInfo($bis_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/reserve/index',[
            'res'  => $table_res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_res'  => $bis_res,
            'bis_id'  => $bis_id
        ]);
    }

    //添加类型页面
    public function add(){
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/reserve/add',[
            'bis_res'  => $bis_res
        ]);
    }

    //添加类型
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        if(empty($param['bis_id'])){
            $this->error('店铺id不能为空!');
        }
        //设置添加到数据库的数据
        $data = [
            'bis_id' => $param['bis_id'],
            'table_name' => $param['table_name'],
            'deposit' => $param['deposit'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $res = model('Reserve')->add($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //编辑
    public function edit(){
        //获取参数
        $id = input('get.id');
        //获取该分类信息
        $res = Db::table('cy_reserve_table_info')->where('id = '.$id)->find();

        return $this->fetch('catering/reserve/edit',[
            'res'  => $res
        ]);
    }

    //删除
    public function remove(){
        //获取参数
        $id = input('get.id');
        $data['status'] = -1;
        $res = Db::table('cy_reserve_table_info')->where('id = '.$id)->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

    //修改
    public function update(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        $param = input('post.');
        //设置添加到数据库的数据
        $data = [
            'table_name' => $param['table_name'],
            'deposit' => $param['deposit'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_reserve_table_info')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

}
