<?php
namespace app\admin\controller;
use think\Controller;
use app\api\controller\Image;
use think\Db;
use think\cache\driver\Redis;

class Recommend extends Base {
    const PAGE_SIZE = 20;
    //推荐位列表
    public function index(){
        $current_page = input('get.current_page',1,'intval');
        $bis_id = input('get.bis_id',0,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //获取总数
        $count = model('Recommend')->getAllRecommendsCount($bis_id);
        $pages = ceil($count / $limit);
        $rec_res = model('Recommend')->getAllRecommends($bis_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('',[
            'rec_res'  => $rec_res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
        ]);
    }

    //平台banner列表
    public function home_index(){
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //获取总数
        $count = model('Recommend')->getAllHomeRecommendsCount();
        $pages = ceil($count / $limit);
        $rec_res = model('Recommend')->getAllHomeRecommends($limit,$offset);
        return $this->fetch('',[
            'rec_res'  => $rec_res,
            'pages'  => $pages,
            'current_page'  => $current_page,
        ]);
    }

    //添加推荐位页面
    public function add(){
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('',[
            'bis_res'  => $bis_res
        ]);
    }

    //添加平台推荐位页面
    public function home_add(){
        return $this->fetch();
    }

    //编辑推荐位
    public function edit(){
        //获取参数
        $id = input('get.id');
        //获取该商品信息
        $rec_res = model('Recommend')->getRecInfoById($id);

        return $this->fetch('',[
            'rec_res'  => $rec_res
        ]);
    }

    //编辑平台推荐位
    public function home_edit(){
        //获取参数
        $id = input('get.id');
        //获取该商品信息
        $rec_res = model('Recommend')->getHomeRecInfoById($id);

        return $this->fetch('',[
            'rec_res'  => $rec_res
        ]);
    }

    //添加推荐位
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //验证数据
        $validate = validate('Recommend');
        if(!$validate->scene('add')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();
        $image_error = $_FILES['image']['error'];


        if($image_error == 0){
            $image_data = $image->uploadS('image','recommend');
            $image_data = str_replace("\\", "/", $image_data);
        }

        //设置添加到数据库的数据
        $recommend_data = [
            'bis_id'  => $param['bis_id'],
            'image' => $image_data,
            'redirect_url' => $param['redirect_url'],
            'type' => $param['type'],
            'listorder' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //普通数据添加到商品表中
        $r_res = model('Recommend')->add($recommend_data);

        if($r_res){
            //更新redis
            $this->updateRedis($param['bis_id']);
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //添加平台推荐位
    public function home_save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //验证数据
        $validate = validate('Recommend');
        if(!$validate->scene('add1')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();
        $image_error = $_FILES['image']['error'];


        if($image_error == 0){
            $image_data = $image->uploadS('image','recommend');
            $image_data = str_replace("\\", "/", $image_data);
        }

        //设置添加到数据库的数据
        $recommend_data = [
            'image' => $image_data,
            'redirect_url' => $param['redirect_url'],
            'listorder' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //普通数据添加到商品表中
        $r_res = model('Recommend')->home_add($recommend_data);

        if($r_res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //修改推荐位信息
    public function update(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        //验证数据
        $validate = validate('Recommend');
        if(!$validate->scene('update')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();

        //设置图片
        if($_FILES['image']['error'] == 0){
            $data['image'] = $image->uploadS('image','recommend');
            $data['image'] = str_replace("\\", "/", $data['image']);
        }


        //设置更新数据
        $data['redirect_url'] = $param['redirect_url'];
        $data['type'] = $param['type'];
        $data['update_time'] = date('Y-m-d H:i:s');

        //更新数据
        Db::table('store_recommend')->where('id = '.$param['res_id'])->update($data);

        //更新redis
        $this->updateRedis($param['bis_id']);

        $this->success("修改成功!");
    }

    //修改平台推荐位信息
    public function home_update(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        //验证数据
        $validate = validate('Recommend');
        if(!$validate->scene('update1')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();

        //设置图片
        if($_FILES['image']['error'] == 0){
            $data['image'] = $image->uploadS('image','recommend');
            $data['image'] = str_replace("\\", "/", $data['image']);
        }


        //设置更新数据
        $data['redirect_url'] = $param['redirect_url'];
        $data['update_time'] = date('Y-m-d H:i:s');

        //更新数据
        Db::table('store_banners')->where('id = '.$param['res_id'])->update($data);

        $this->success("修改成功!");
    }

    //排序
    public function listorder(){
        if(!request()->isPost()){
            return show(0,'请求方式错误');
        }
        //获取参数
        $id = input('post.id');
        $listorder = input('post.listorder');
        $data['listorder'] = $listorder;

        $res = Db::table('store_recommend')->where('id = '.$id)->update($data);

        //获取bis_id
        $bis_id = model('Recommend')->getBisIdById($id);
        //更新redis
        $this->updateRedis($bis_id);

        if($res){
            return show(1,'success',$_SERVER['HTTP_REFERER']);
        }else{
            return show(1,'error');
        }
    }

    //排序
    public function home_listorder(){
        if(!request()->isPost()){
            return show(0,'请求方式错误');
        }
        //获取参数
        $id = input('post.id');
        $listorder = input('post.listorder');
        $data['listorder'] = $listorder;

        $res = Db::table('store_banners')->where('id = '.$id)->update($data);

        if($res){
            return show(1,'success',$_SERVER['HTTP_REFERER']);
        }else{
            return show(1,'error');
        }
    }

    //更改状态
    public function updateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('store_recommend')->where('id = '.$id)->update($data);

        if($res){
            //获取bis_id
            $bis_id = model('Recommend')->getBisIdById($id);
            //更新redis
            $this->updateRedis($bis_id);
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //更改状态
    public function home_updateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('store_banners')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //更新redis
    public function updateRedis($bis_id){
        $redis = new Redis();
        $redis_key = "banners_list_".$bis_id;
        $res = model('Recommend')->getBanners($bis_id);
        $json = json_encode($res);
        $redis->set($redis_key,$json);
    }

    //**************************************************
    //以下为餐饮部分
    //banner列表

    public function cy_index(){
        $bis_id = input('get.bis_id',0,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //获取总数
        $count = model('Recommend')->getCatAllRecommendsCount($bis_id);
        $pages = ceil($count / $limit);
        $rec_res = model('Recommend')->getCatAllRecommends($bis_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/banner/index',[
            'rec_res'  => $rec_res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_res'  => $bis_res,
            'bis_id'  => $bis_id,
        ]);
    }

    //添加banner页面
    public function catAdd(){
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/banner/add',['bis_res' => $bis_res]);
    }


    //编辑banner
    public function catEdit(){
        //获取参数
        $id = input('get.id');
        //获取该商品信息
        $rec_res = model('Recommend')->getCatRecInfoById($id);

        return $this->fetch('catering/banner/edit',[
            'rec_res'  => $rec_res
        ]);
    }

    //添加banner
    public function catSave(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //验证数据
        $validate = validate('Recommend');
        if(!$validate->scene('catAdd')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();
        $image_error = $_FILES['image']['error'];


        if($image_error == 0){
            $image_data = $image->uploadS('image','banner');
            $image_data = str_replace("\\", "/", $image_data);
        }

        //设置添加到数据库的数据
        $recommend_data = [
            'bis_id'  => $param['bis_id'],
            'image' => $image_data,
            'redirect_url' => $param['redirect_url'],
            'listorder' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //普通数据添加到商品表中
        $r_res = model('Recommend')->catAdd($recommend_data);

        if($r_res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }


    //修改banner信息
    public function catUpdate(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        //验证数据
        $validate = validate('Recommend');
        if(!$validate->scene('catUpdate')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();

        //设置图片
        if($_FILES['image']['error'] == 0){
            $data['image'] = $image->uploadS('image','recommend');
            $data['image'] = str_replace("\\", "/", $data['image']);
        }


        //设置更新数据
        $data['redirect_url'] = $param['redirect_url'];
        $data['update_time'] = date('Y-m-d H:i:s');

        //更新数据
        Db::table('cy_banners')->where('id = '.$param['res_id'])->update($data);

        $this->success("修改成功!");
    }

    //排序
    public function catListorder(){
        if(!request()->isPost()){
            return show(0,'请求方式错误');
        }
        //获取参数
        $id = input('post.id');
        $listorder = input('post.listorder');
        $data['listorder'] = $listorder;

        $res = Db::table('cy_banners')->where('id = '.$id)->update($data);

        if($res){
            return show(1,'success',$_SERVER['HTTP_REFERER']);
        }else{
            return show(1,'error');
        }

    }

    //更改状态
    public function catUpdateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('cy_banners')->where('id = '.$id)->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }


}
