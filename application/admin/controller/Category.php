<?php
namespace app\admin\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Category extends Base {

    //商家分类列表
    public function index(){
        $current_page = input('get.current_page',1,'intval');
        $parent_id = input('get.parent_id',0,'intval');
        $bis_id = input('get.bis_id',0,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('DefinedCategory')->getCategorysCount($bis_id,$parent_id);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('DefinedCategory')->getCategorys($bis_id,$parent_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'parent_id'  => $parent_id,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
        ]);
    }

    //平台分类列表
    public function home_index(){
        $current_page = input('get.current_page',1,'intval');
        $parent_id = input('get.parent_id',0,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Category')->getCategorysCount($parent_id);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Category')->getCategorys($parent_id,$limit,$offset);
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'parent_id'  => $parent_id,
        ]);
    }

    //添加分类页面
    public function add(){
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('',[
            'bis_res'  => $bis_res
        ]);
    }

    //添加平台分类页面
    public function home_add(){
        $res = model('Category')->getNormalFirstCategory();
        return $this->fetch('',[
            'res'  => $res
        ]);
    }

    //编辑分类
    public function edit(){
        //获取参数
        $id = input('get.id');
        //获取该分类信息
        $category = Db::table('store_defined_category')->where('id = '.$id)->find();
        $categorys = model('DefinedCategory')->getNormalFirstCat();

        return $this->fetch('',[
            'category'  => $category,
            'categorys'  => $categorys,
        ]);
    }

    //编辑分类
    public function home_edit(){
        //获取参数
        $id = input('get.id');
        //获取该分类信息
        $category = Db::table('store_category')->where('id = '.$id)->find();
        $categorys = model('Category')->getNormalFirstCat();

        return $this->fetch('',[
            'category'  => $category,
            'categorys'  => $categorys,
        ]);
    }

    //添加分类
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        //验证数据
        $validate = validate('DefinedCategory');
        if(!$validate->scene('add')->check($param)){
            $this->error($validate->getError());
        }

        //设置添加到数据库的数据
        $data = [
            'bis_id' => $param['bis_id'],
            'cat_name' => $param['cat_name'],
            'parent_id' => $param['parent_id'],
            'listorder' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        $res = model('DefinedCategory')->add($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //添加平台分类
    public function home_save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        //验证数据
        $validate = validate('Category');
        if(!$validate->scene('add')->check($param)){
            $this->error($validate->getError());
        }

        //设置添加到数据库的数据
        $data = [
            'cat_name' => $param['cat_name'],
            'parent_id' => $param['parent_id'],
            'listorder' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = model('Category')->add($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //根据父id查询分类信息
    public function getCategorysInfo(){
        $parent_id = input('post.parent_id',0,'intval');
        $res = model('DefinedCategory')->getCategorysByParentId($parent_id);

        if($res){
            return show(1,'success',$res);
        }else{
            return show(0,'error');
        }
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

        $res = Db::table('store_defined_category')->where('id = '.$id)->update($data);

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

        $res = Db::table('store_category')->where('id = '.$id)->update($data);

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
        $res = Db::table('store_defined_category')->where('id = '.$id)->update($data);

        if($res){
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
        $res = Db::table('store_category')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改分类
    public function updateCategory(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        //验证数据
        $validate = validate('DefinedCategory');
        if(!$validate->scene('update')->check($param)){
            $this->error($validate->getError());
        }

        //设置添加到数据库的数据
        $data = [
            'cat_name' => $param['cat_name'],
            'parent_id' => $param['parent_id'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('store_defined_category')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

    //修改分类
    public function home_updateCategory(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');
        //验证数据
        $validate = validate('Category');
        if(!$validate->scene('update')->check($param)){
            $this->error($validate->getError());
        }

        //设置添加到数据库的数据
        $data = [
            'cat_name' => $param['cat_name'],
            'parent_id' => $param['parent_id'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('store_category')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

    //添加商品页面获取一级分类信息
    public function getNormalFirstCategory(){
        $bis_id = input('post.bis_id');
        $defined_category = model('DefinedCategory')->getNormalFirstCategory($bis_id);
        if($defined_category){
            return show(1,'success',$defined_category);
        }else{
            return show(0,'error');
        }
    }

    //添加商品页面根据父id获取分类信息
    public function getCategoryByParentId(){

        $category_id = input('post.category_id',0,'intval');

        if(!$category_id){
            $this->error('ID不合法');
        }
        //通过id获取二级分类
        $categorys = model('DefinedCategory')->getNormalCategoryByParentId($category_id);

        if(!$categorys){
            return show(0,'error');
        }
        return show(1,'success',$categorys);
    }

}
