<?php
namespace app\admin\controller;
use think\Controller;
use app\api\controller\Image;
use think\Db;

class Advertisement extends Base {

    const PAGE_SIZE = 10;

    //广告位列表
    public function index(){
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Advertisement')->getAdsCount();
        //总页码
        $pages = ceil($count / $limit);
        $res = model('Advertisement')->getAds($limit,$offset);

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page
        ]);
    }

    public function add(){
        return $this->fetch('');
    }

    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'jump_url' => $param['jump_url'],
            'type' => $param['type'],
            'created_time' => date('Y-m-d H:i:s'),
            'updated_time' => date('Y-m-d H:i:s')
        ];

        //上传图片相关
        $image = new Image();
        $img_error = !empty($_FILES['img_url']) ? $_FILES['img_url']['error'] : -1;

        if($img_error == 0){
            $image_data = $image->uploadS('img_url','ads');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img_url'] = self::IMG_URL.$image_data;
        }

        $res = Db::table('store_ads')->insert($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    public function edit(){
        $id = input('id');
        $res = model('Advertisement')->getAdInfoById($id);
        return $this->fetch('',[
            'res'  => $res
        ]);
    }

    public function update(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');
        $id = $param['id'];
        //设置添加到数据库的数据
        $data = [
            'jump_url' => $param['jump_url'],
            'type' => $param['type'],
            'updated_time' => date('Y-m-d H:i:s')
        ];

        //上传图片相关
        $image = new Image();
        $img_error = !empty($_FILES['img_url']) ? $_FILES['img_url']['error'] : -1;

        if($img_error == 0){
            $image_data = $image->uploadS('img_url','ads');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img_url'] = self::IMG_URL.$image_data;
        }

        $res = Db::table('store_ads')->where("id = $id")->update($data);

        if($res){
            $this->success("更新成功");
        }else{
            $this->error('更新失败');
        }
    }

    public function remove(){
        //获取提交的数据
        $id = input('get.id');
        $data = ['status'  => -1];
        $res = Db::table('store_ads')->where("id = $id")->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

    public function changeOrder(){
        $id = input('post.id');
        $order = input('post.order');

        $data = ['listorder'  => $order];

        $res = Db::table('store_ads')->where("id = $id")->update($data);

        if($res){
            return show(1,'success',$_SERVER['HTTP_REFERER']);
        }else{
            return show(1,'error');
        }
    }

    //***********************************
    //以下是餐饮部分  广告位  代码
    //广告位列表
    //广告位列表
    public function cat_index(){
        $current_page = input('get.current_page',1,'intval');
        $bis_id = input('get.bis_id',0);
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Advertisement')->getCatAdsCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        $res = model('Advertisement')->getCatAds($bis_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/advertisement/index',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_res'  => $bis_res,
            'bis_id'  => $bis_id
        ]);
    }

    public function changeCatOrder(){
        $id = input('post.id');
        $order = input('post.order');

        $data = ['listorder'  => $order];

        $res = Db::table('cy_ads')->where("id = $id")->update($data);

        if($res){
            return show(1,'success',$_SERVER['HTTP_REFERER']);
        }else{
            return show(1,'error');
        }
    }

    public function catAdd(){
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/advertisement/add',[
            'bis_res'  => $bis_res
        ]);
    }

    public function catSave(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => $param['bis_id'],
            'jump_url' => $param['jump_url'],
            'created_time' => date('Y-m-d H:i:s'),
            'updated_time' => date('Y-m-d H:i:s')
        ];

        //上传图片相关
        $image = new Image();
        $img_error = !empty($_FILES['img_url']) ? $_FILES['img_url']['error'] : -1;

        if($img_error == 0){
            $image_data = $image->uploadS('img_url','ads');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img_url'] = self::IMG_URL.$image_data;
        }

        $res = Db::table('cy_ads')->insert($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    public function catEdit(){
        $id = input('id');
        $res = model('Advertisement')->getCatAdInfoById($id);
        return $this->fetch('catering/advertisement/edit',[
            'res'  => $res
        ]);
    }

    public function catUpdate(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');
        $id = $param['id'];
        //设置添加到数据库的数据
        $data = [
            'jump_url' => $param['jump_url'],
            'updated_time' => date('Y-m-d H:i:s')
        ];

        //上传图片相关
        $image = new Image();
        $img_error = !empty($_FILES['img_url']) ? $_FILES['img_url']['error'] : -1;

        if($img_error == 0){
            $image_data = $image->uploadS('img_url','ads');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img_url'] = self::IMG_URL.$image_data;
        }

        $res = Db::table('cy_ads')->where("id = $id")->update($data);

        if($res){
            $this->success("更新成功");
        }else{
            $this->error('更新失败');
        }
    }

    public function catRemove(){
        //获取提交的数据
        $id = input('get.id');
        $data = ['status'  => -1];
        $res = Db::table('cy_ads')->where("id = $id")->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

}
