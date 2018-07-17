<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class DefinedCategory extends Model{

    //添加分类
    public function add($data){
        $data['status'] = 1;
        return Db::table('store_defined_category')->insert($data);
    }

    //获取一级分类信息
    public function getNormalFirstCategory($bis_id){
        $data = [
            'status' => 1,
            'parent_id' => 0,
            'bis_id' => $bis_id
        ];

        $order = [
            'id'  => 'desc'
        ];

        return Db::table('store_defined_category')->where($data)->order($order)->select();
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

        return Db::table('store_defined_category')->where($data)->order($order)->select();
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

        return Db::table('store_defined_category')->where($data)->order($order)->select();
    }

    //获取所有分类信息
    public function getCategorys($bis_id,$parent_id = 0,$limit,$offset){
        if($bis_id == 0){
            $data = [
                'dc.parent_id'  => $parent_id,
                'dc.status'  => ['neq',-1]
            ];
        }else{
            $data = [
                'dc.parent_id'  => $parent_id,
                'dc.status'  => ['neq',-1],
                'dc.bis_id'  => $bis_id
            ];
        }

        $order = [
            'dc.listorder'  => 'desc',
            'dc.id'  => 'desc'
        ];

        $result = Db::table('store_defined_category')->alias('dc')->field('dc.id,dc.bis_id,dc.cat_name,dc.parent_id,dc.listorder,dc.create_time,dc.update_time,dc.status,bis.bis_name')
                ->join('store_bis bis','dc.bis_id = bis.id','LEFT')
                ->where($data)
                ->order($order)
                ->limit($offset,$limit)
                ->select();

        return $result;
    }

    //获取所有分类数量
    public function getCategorysCount($bis_id,$parent_id = 0){
        if($bis_id == 0){
            $data = [
                'parent_id'  => $parent_id,
                'status'  => ['neq',-1]
            ];
        }else{
            $data = [
                'parent_id'  => $parent_id,
                'status'  => ['neq',-1],
                'bis_id'  => $bis_id
            ];
        }

        $order = [
            'listorder'  => 'desc',
            'id'  => 'desc'
        ];

        $result = Db::table('store_defined_category')->where($data)->count();

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

        return Db::table('store_defined_category')->where($data)->order($order)->select();
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

        $result = Db::table('store_defined_category')->where($data)
            ->order($order)
            ->select();

        return $result;
    }
}

?>