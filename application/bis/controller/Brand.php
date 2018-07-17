<?php
namespace app\bis\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Brand extends Base {

    //品牌列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Brand')->getBrandCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        $res = model('Brand')->getBrands($bis_id,$limit,$offset);
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page
        ]);
    }

    //添加品牌页面
    public function add(){
        return $this->fetch();
    }


    //编辑品牌信息
    public function edit(){
        //获取参数
        $id = input('get.id');
        //获取该分类信息
        $res = Db::table('store_brand')->where('id = '.$id)->find();

        return $this->fetch('',[
            'res'  => $res,
        ]);
    }

    //添加品牌
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //验证数据
        $validate = validate('Brand');
        if(!$validate->scene('add')->check($param)){
            $this->error($validate->getError());
        }

        //设置添加到数据库的数据
        $data = [
            'bis_id' => session('bis_id','','bis'),
            'brand_name' => $param['brand_name'],
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        $res = model('Brand')->add($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }


    //更改状态
    public function updateStatus(){
        //获取参数
        $param = input('get.');
        $id = $param['id'];
        $status = $param['status'];

        $validate = validate('Brand');
        if(!$validate->scene('status')->check($param)){
            $this->error($validate->getError());
        }

        $data['status'] = $status;
        $res = Db::table('store_brand')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改品牌信息
    public function updateBrand(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        //验证数据
        $validate = validate('Brand');
        if(!$validate->scene('update')->check($param)){
            $this->error($validate->getError());
        }

        //设置添加到数据库的数据
        $data = [
            'brand_name' => $param['brand_name'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('store_brand')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

}
