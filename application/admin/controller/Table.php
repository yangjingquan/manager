<?php
namespace app\admin\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Table extends Base {
    const PAGE_SIZE = 20;
    //桌位列表
    public function index(){
        $bis_id = input('get.bis_id',0,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Table')->getTablesCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Table')->getTables($bis_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/table/index',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_res'  => $bis_res,
            'bis_id'  => $bis_id
        ]);
    }

    //添加桌位页面
    public function add(){
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/table/add',['bis_res' => $bis_res]);
    }


    //编辑桌位
    public function edit(){
        //获取参数
        $id = input('get.id');
        //获取该桌位信息
        $table = Db::table('cy_tables')->where('id = '.$id)->find();

        return $this->fetch('catering/table/edit',[
            'table'  => $table
        ]);
    }

    //添加桌位
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => $param['bis_id'],
            'table_name' => $param['table_name'],
            'create_time' => date('Y-m-d H:i:s')
        ];
        
        $res = model('Table')->add($data);

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
        $res = Db::table('cy_tables')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //更改桌位状态
    public function updateTableStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.show');
        $data['shows'] = $status;
        $res = Db::table('cy_tables')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改桌位
    public function updateTable(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'table_name' => $param['table_name']
        ];

        $res = Db::table('cy_tables')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

}
