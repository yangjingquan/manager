<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class CatCategory extends Model{

    //添加分类
    public function add($data){
        $data['status'] = 1;
        return Db::table('cy_category')->insert($data);
    }

    //获取一级分类信息
    public function getNormalFirstCategory($bis_id){
        $data = [
            'status' => 1,
            'bis_id'  => $bis_id
        ];

        $order = [
            'id'  => 'desc'
        ];

        return Db::table('cy_category')->where($data)->order($order)->select();
    }

    //获取二级分类信息
    public function getNormalSecondCategory(){
        $data = [
            'status' => 1
        ];

        $order = [
            'id'  => 'desc'
        ];

        return Db::table('cy_category')->where($data)->order($order)->select();
    }

    //获取所有分类信息
    public function getCategorys($bis_id,$limit,$offset){
        $data = [
            'status'  => ['neq',-1],
            'bis_id'   => $bis_id
        ];

        $order = [
            'listorder'  => 'desc',
            'id'  => 'desc'
        ];

        $result = Db::table('cy_category')
            ->where($data)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $result;
    }

    //获取所有分类信息
    public function getAllCategorys($bis_id,$limit,$offset){
        if($bis_id == 0){
            $data = [
                'dc.status'  => ['neq',-1],
                'bis.status'  => 1
            ];
        }else{
            $data = [
                'dc.status'  => ['neq',-1],
                'dc.bis_id'  => $bis_id,
                'bis.status'  => 1
            ];
        }

        $order = [
            'dc.listorder'  => 'desc',
            'dc.id'  => 'desc'
        ];

        $result = Db::table('cy_category')->alias('dc')->field('dc.id,dc.bis_id,dc.cat_name,dc.listorder,dc.create_time,dc.update_time,dc.status,bis.bis_name')
            ->join('cy_bis bis','dc.bis_id = bis.id','LEFT')
            ->where($data)
            ->order($order)
            ->limit($offset,$limit)
            ->select();

        return $result;
    }

    //获取所有分类数量
    public function getCategorysCount($bis_id){
        $data = [
            'status'  => ['neq',-1],
            'bis_id'   => $bis_id
        ];

        $order = [
            'listorder'  => 'desc',
            'id'  => 'desc'
        ];

        $result = Db::table('cy_category')->where($data)->count();

        return $result;
    }

    //获取所有分类数量
    public function getAllCategorysCount(){
        $data = [
            'status'  => ['neq',-1]
        ];

        $result = Db::table('cy_category')->where($data)->count();

        return $result;
    }

    //通过父id获取分类信息
    public function getNormalCategoryByParentId(){
        $data = [
            'status' => 1
        ];

        $order = [
            'id'  => 'desc'
        ];

        return Db::table('cy_category')->where($data)->order($order)->select();
    }

    //通过父id获取分类信息
    public function getCategorysByParentId(){
        $data = [
            'status'  => 1
        ];

        $order = [
            'listorder'  => 'desc',
            'id'  => 'desc'
        ];

        $result = Db::table('cy_category')->where($data)
            ->order($order)
            ->select();

        return $result;
    }

}

?>