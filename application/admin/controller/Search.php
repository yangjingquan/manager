<?php
namespace app\admin\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Search extends Base {

    //热词列表
    public function index(){
        $bis_id = input('get.bis_id',0,'intval');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Search')->getHotWordsCount($bis_id);
        //总页码
        $pages = ceil($count / $limit);
        $res = model('Search')->getHotWords($bis_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();

        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res
        ]);
    }

    //添加热词页面
    public function add(){
        return $this->fetch();
    }


    //编辑热词信息
    public function edit(){
        //获取参数
        $id = input('get.id');
        //获取该分类信息
        $res = Db::table('store_search_words')->where('id = '.$id)->find();

        return $this->fetch('',[
            'res'  => $res,
        ]);
    }

    //添加热词
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //验证数据
        $validate = validate('Search');
        if(!$validate->scene('add')->check($param)){
            $this->error($validate->getError());
        }

        //设置添加到数据库的数据
        $data = [
            'bis_id' => session('bis_id','','bis'),
            'word' => $param['word'],
            'create_time' => date('Y-m-d H:i:s'),
        ];
        
        $res = model('Search')->add($data);

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
        $res = Db::table('store_search_words')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改热词信息
    public function updateSearchWord(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        //验证数据
        $validate = validate('Search');
        if(!$validate->scene('update')->check($param)){
            $this->error($validate->getError());
        }

        //设置添加到数据库的数据
        $data = [
            'word' => $param['word']
        ];

        $res = Db::table('store_search_words')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

}
