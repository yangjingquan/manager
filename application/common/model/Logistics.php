<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Logistics extends Model{

    //获取物流模板信息
    public function getTemplates($bis_id,$limit,$offset,$post_mode){
        if($post_mode && $post_mode != '全部'){
            $where = " and mode.post_mode like '%$post_mode%'";
        }else{
            $where = '';
        }
        $res = Db::table('store_logistics_template')->alias('tem')->field('tem.id as tem_id,mode.post_mode,tem.province,tem.first_heavy,tem.continue_heavy')
            ->join('store_post_mode mode','tem.mode_id = mode.id','LEFT')
            ->where('tem.bis_id ='.$bis_id .' and tem.status = 1'.$where)
            ->order('tem.id desc')
            ->limit($offset,$limit)
            ->select();


        return $res;
    }

    //获取物流模板信息
    public function getTemplateCount($bis_id,$post_mode){
        if($post_mode && $post_mode != '全部'){
            $where = " and mode.post_mode like '%$post_mode%'";
        }else{
            $where = '';
        }
        $res = Db::table('store_logistics_template')->alias('tem')
            ->join('store_post_mode mode','tem.mode_id = mode.id','LEFT')
            ->where('tem.bis_id ='.$bis_id .' and tem.status = 1'.$where)
            ->order('tem.id desc')
            ->count();

        return $res;
    }

    //获取快递种类
    public function getPostMode(){
        $res = Db::table('store_post_mode')->field('id,post_mode')->where('status = 1')->select();
        return $res;
    }

    //添加模板信息
    public function add($data){
        $res = Db::table('store_logistics_template')->insert($data);
        return $res;
    }

    //根据id获取模板信息
    public function getTemRes($id){
        $res = Db::table('store_logistics_template')->where('id = '.$id)->find();
        return $res;
    }
}

?>