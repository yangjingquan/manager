<?php
namespace app\bis\controller;
use think\Controller;
use app\api\controller\Image;
use think\Db;

class Recommend extends Base {

    //推荐位列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //获取总数
        $count = model('Recommend')->getAllRecommendsCount($bis_id);
        $pages = ceil($count / $limit);
        $rec_res = model('Recommend')->getAllRecommends($bis_id,$limit,$offset);
        return $this->fetch('',[
            'rec_res'  => $rec_res,
            'pages'  => $pages,
            'current_page'  => $current_page,
        ]);
    }

    //添加推荐位页面
    public function add(){
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
            'bis_id'  => session('bis_id','','bis'),
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
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }


}
