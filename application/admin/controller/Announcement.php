<?php
namespace app\admin\controller;
use think\Controller;
use app\api\controller\Image;
use think\Validate;
use think\Db;

class Announcement extends Base {
    const PAGE_SIZE = 20;
    //公告列表
    public function index(){
        $bis_id = input('get.bis_id',0,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        if($bis_id != 0){
            $where = "a.bis_id = ".$bis_id.' and ';
        }else{
            $where = '';
        }
        $count = Db::table('store_announcement')->alias('a')
            ->join('store_bis b','a.bis_id = b.id','LEFT')
            ->where($where."a.status = 1 and b.status = 1")
            ->count();

        //总页码
        $pages = ceil($count / $limit);
        $res = Db::table('store_announcement')->alias('a')->field('a.id,a.title,a.content,a.show_status,a.status,b.bis_name')
            ->join('store_bis b','a.bis_id = b.id','LEFT')
            ->where($where."a.status = 1 and b.status = 1")
            ->limit($offset,$limit)
            ->order('a.create_time desc')
            ->select();

        $index = 0;
        foreach($res as $val){
            if(strlen($val['content']) > 40){
                $res[$index]['content'] = substr($val['content'],0,40).'...';
            }else{
                $res[$index]['content'] = $val['content'];
            }

            $index ++;
        }

        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  =>  $count,
            'bis_id'  => $bis_id,
            'bis_res'  =>  $bis_res
        ]);
    }

    //公告列表(总站)
    public function home_index(){
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = Db::table('store_home_announcement')->where("status = 1")->count();
        //总页码
        $pages = ceil($count / $limit);
        $res = Db::table('store_home_announcement')->where("status = 1")
            ->limit($offset,$limit)
            ->order('create_time desc')
            ->select();

        $index = 0;
        foreach($res as $val){
            if(strlen($val['content']) > 40){
                $res[$index]['content'] = substr($val['content'],0,40).'...';
            }else{
                $res[$index]['content'] = $val['content'];
            }

            $index ++;
        }

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  =>  $count
        ]);
    }

    //添加公告页面
    public function add(){
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('',[
            'bis_res'  => $bis_res
        ]);
    }

    //添加公告页面(总站)
    public function home_add(){
        return $this->fetch();
    }

    //添加商品
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');
        $bis_id = input('post.bis_id',0,'intval');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => $bis_id,
            'title' => $param['title'],
            'content' => $param['content'],
            'create_time' => date('Y-m-d H:i:s')
        ];

        //上传图片相关
        $image = new Image();
        $img1_error = !empty($_FILES['img1']) ? $_FILES['img1']['error'] : -1;
        $img2_error = !empty($_FILES['img2']) ? $_FILES['img2']['error'] : -1;

        if($img1_error == 0){
            $image_data = $image->uploadS('img1','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img1'] = $image_data;
        }

        if($img2_error == 0){
            $image_data = $image->uploadS('img2','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img2'] = $image_data;
        }

        $res = Db::table('store_announcement')->insert($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //添加商品(总站)
    public function home_save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'title' => $param['title'],
            'content' => $param['content'],
            'create_time' => date('Y-m-d H:i:s')
        ];

        //上传图片相关
        $image = new Image();
        $img1_error = !empty($_FILES['img1']) ? $_FILES['img1']['error'] : -1;
        $img2_error = !empty($_FILES['img2']) ? $_FILES['img2']['error'] : -1;

        if($img1_error == 0){
            $image_data = $image->uploadS('img1','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img1'] = $image_data;
        }

        if($img2_error == 0){
            $image_data = $image->uploadS('img2','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img2'] = $image_data;
        }

        $res = Db::table('store_home_announcement')->insert($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //更改状态
    public function updateShowStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.show_status');
        $data['show_status'] = $status;
        $res = Db::table('store_announcement')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //更改状态(总站)
    public function home_updateShowStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.show_status');
        $data['show_status'] = $status;
        $res = Db::table('store_home_announcement')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //删除
    public function remove(){
        //获取参数
        $id = input('get.id');
        $data['status'] = -1;
        $res = Db::table('store_announcement')->where('id = '.$id)->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

    //删除(总站)
    public function home_remove(){
        //获取参数
        $id = input('get.id');
        $data['status'] = -1;
        $res = Db::table('store_home_announcement')->where('id = '.$id)->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

    //编辑公告
    public function edit(){
        //获取参数
        $id = input('get.id');
        $res = Db::table('store_announcement')->where('id = '.$id)->find();

        return $this->fetch('',[
            'res'  => $res
        ]);
    }

    //编辑公告(总站)
    public function home_edit(){
        //获取参数
        $id = input('get.id');
        $res = Db::table('store_home_announcement')->where('id = '.$id)->find();

        return $this->fetch('',[
            'res'  => $res
        ]);
    }

    //修改公告
    public function updateAnnouncement(){
        //获取参数
        $id = input('post.a_id');
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'title' => $param['title'],
            'content' => $param['content']
        ];

        //上传图片相关
        $image = new Image();
        $img1_error = !empty($_FILES['img1']) ? $_FILES['img1']['error'] : -1;
        $img2_error = !empty($_FILES['img2']) ? $_FILES['img2']['error'] : -1;

        if($img1_error == 0){
            $image_data = $image->uploadS('img1','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img1'] = $image_data;
        }

        if($img2_error == 0){
            $image_data = $image->uploadS('img2','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img2'] = $image_data;
        }

        $res = Db::table('store_announcement')->where('id = '.$id)->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

    //修改公告(总站)
    public function home_updateAnnouncement(){
        //获取参数
        $id = input('post.a_id');
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'title' => $param['title'],
            'content' => $param['content']
        ];

        //上传图片相关
        $image = new Image();
        $img1_error = !empty($_FILES['img1']) ? $_FILES['img1']['error'] : -1;
        $img2_error = !empty($_FILES['img2']) ? $_FILES['img2']['error'] : -1;

        if($img1_error == 0){
            $image_data = $image->uploadS('img1','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img1'] = $image_data;
        }

        if($img2_error == 0){
            $image_data = $image->uploadS('img2','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img2'] = $image_data;
        }

        $res = Db::table('store_home_announcement')->where('id = '.$id)->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

    //***********************************
    //以下是餐饮部分接口
    //公告列表
    public function cy_index(){
        $bis_id = input('get.bis_id',0,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        if($bis_id != 0){
            $where = "a.bis_id = ".$bis_id.' and ';
        }else{
            $where = '';
        }
        $count = Db::table('cy_announcement')->alias('a')
            ->join('cy_bis b','a.bis_id = b.id','LEFT')
            ->where($where."a.status = 1 and b.status = 1")
            ->count();

        //总页码
        $pages = ceil($count / $limit);
        $res = Db::table('cy_announcement')->alias('a')->field('a.id,a.title,a.content,a.show_status,a.status,b.bis_name')
            ->join('cy_bis b','a.bis_id = b.id','LEFT')
            ->where($where."a.status = 1 and b.status = 1")
            ->limit($offset,$limit)
            ->order('a.create_time desc')
            ->select();

        $index = 0;
        foreach($res as $val){
            if(strlen($val['content']) > 40){
                $res[$index]['content'] = substr($val['content'],0,40).'...';
            }else{
                $res[$index]['content'] = $val['content'];
            }

            $index ++;
        }

        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();

        return $this->fetch('catering/announcement/index',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  =>  $count,
            'bis_id'  => $bis_id,
            'bis_res'  =>  $bis_res
        ]);
    }

    //添加公告页面
    public function catAdd(){
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/announcement/add',[
            'bis_res'  => $bis_res
        ]);
    }

    //添加商品
    public function catSave(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');
        $bis_id = input('post.bis_id',0,'intval');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => $bis_id,
            'title' => $param['title'],
            'content' => $param['content'],
            'create_time' => date('Y-m-d H:i:s')
        ];

        //上传图片相关
        $image = new Image();
        $img1_error = !empty($_FILES['img1']) ? $_FILES['img1']['error'] : -1;
        $img2_error = !empty($_FILES['img2']) ? $_FILES['img2']['error'] : -1;

        if($img1_error == 0){
            $image_data = $image->uploadS('img1','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img1'] = $image_data;
        }

        if($img2_error == 0){
            $image_data = $image->uploadS('img2','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img2'] = $image_data;
        }

        $res = Db::table('cy_announcement')->insert($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //更改状态
    public function updateCatShowStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.show_status');
        $data['show_status'] = $status;
        $res = Db::table('cy_announcement')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //删除
    public function catRemove(){
        //获取参数
        $id = input('get.id');
        $data['status'] = -1;
        $res = Db::table('cy_announcement')->where('id = '.$id)->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

    //编辑公告
    public function CatEdit(){
        //获取参数
        $id = input('get.id');
        $res = Db::table('cy_announcement')->where('id = '.$id)->find();

        return $this->fetch('catering/announcement/edit',[
            'res'  => $res
        ]);
    }

    //修改公告
    public function updateCatAnnouncement(){
        //获取参数
        $id = input('post.a_id');
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'title' => $param['title'],
            'content' => $param['content']
        ];

        //上传图片相关
        $image = new Image();
        $img1_error = !empty($_FILES['img1']) ? $_FILES['img1']['error'] : -1;
        $img2_error = !empty($_FILES['img2']) ? $_FILES['img2']['error'] : -1;

        if($img1_error == 0){
            $image_data = $image->uploadS('img1','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img1'] = $image_data;
        }

        if($img2_error == 0){
            $image_data = $image->uploadS('img2','announcement');
            $image_data = str_replace("\\", "/", $image_data);
            $data['img2'] = $image_data;
        }

        $res = Db::table('cy_announcement')->where('id = '.$id)->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

}
