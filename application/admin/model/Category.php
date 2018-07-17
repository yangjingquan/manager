<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Category extends Model{

    //添加分类
    public function add($data){
        $data['status'] = 1;
        return Db::table('store_category')->insert($data);
    }

    //获取一级分类信息
    public function getNormalFirstCategory(){
        $data = [
            'status' => 1,
            'parent_id' => 0
        ];

        $order = [
            'id'  => 'desc'
        ];

        return Db::table('store_category')->where($data)->order($order)->select();
    }

    //获取一级分类信息(不带店铺id)
    public function getNormalFirstCat(){
        $data = [
            'status' => 1,
            'parent_id' => 0
        ];

        $order = [
            'id'  => 'desc'
        ];

        return Db::table('store_category')->where($data)->order($order)->select();
    }


    //获取二级分类信息
    public function getNormalSecondCategory($parent_id){
        $data = [
            'status' => 1,
            'parent_id' => $parent_id
        ];

        $order = [
            'id'  => 'desc'
        ];

        return Db::table('store_category')->where($data)->order($order)->select();
    }

    //获取所有分类信息
    public function getCategorys($parent_id = 0,$limit,$offset){
        $data = [
            'c.parent_id'  => $parent_id,
            'c.status'  => ['neq',-1]
        ];

        $order = [
            'c.listorder'  => 'desc',
            'c.id'  => 'desc'
        ];

        $result = Db::table('store_category')->alias('c')->field('c.id,c.cat_name,c.parent_id,c.listorder,c.create_time,c.update_time,c.status')
            ->where($data)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $result;
    }

    //获取所有分类数量
    public function getCategorysCount($parent_id = 0){
        $data = [
            'parent_id'  => $parent_id,
            'status'  => ['neq',-1]
        ];

        $order = [
            'listorder'  => 'desc',
            'id'  => 'desc'
        ];

        $result = Db::table('store_category')->where($data)->count();

        return $result;
    }

    //通过父id获取分类信息
    public function getNormalCategoryByParentId($parent_id = 0){
        $data = [
            'status' => 1,
            'parent_id' => $parent_id
        ];

        $order = [
            'id'  => 'desc'
        ];

        return Db::table('store_category')->where($data)->order($order)->select();
    }

    //通过父id获取分类信息
    public function getCategorysByParentId($parent_id = 0){
        $data = [
            'parent_id'  => $parent_id,
            'status'  => 1
        ];

        $order = [
            'listorder'  => 'desc',
            'id'  => 'desc'
        ];

        $result = Db::table('store_category')->where($data)
            ->order($order)
            ->select();

        return $result;
    }
}

?>