<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Logistics extends Model{

    //获取物流模板信息
    public function getTemplates($bis_id,$limit,$offset,$post_mode){
        if($bis_id != 0){
            $where = "tem.bis_id = $bis_id and bis.transport_type = 1 and ";
        }else{
            $where = "bis.transport_type = 1 and ";
        }
        if($post_mode && $post_mode != '全部'){
            $where1 = " and mode.post_mode like '%$post_mode%'";
        }else{
            $where1 = '';
        }
        $res = Db::table('store_logistics_template')->alias('tem')->field('tem.id as tem_id,mode.post_mode,tem.province,tem.first_heavy,tem.continue_heavy,bis.bis_name')
            ->join('store_post_mode mode','tem.mode_id = mode.id','LEFT')
            ->join('store_bis bis','tem.bis_id = bis.id','LEFT')
            ->where($where.'tem.status = 1'.$where1)
            ->order('tem.id desc')
            ->limit($offset,$limit)
            ->select();

        return $res;
    }

    //获取物流模板信息
    public function getTemplateCount($bis_id,$post_mode){
        if($bis_id != 0){
            $where = "tem.bis_id = $bis_id and bis.transport_type = 1 and ";
        }else{
            $where = "bis.transport_type = 1 and ";
        }
        if($post_mode && $post_mode != '全部'){
            $where1 = " and mode.post_mode like '%$post_mode%'";
        }else{
            $where1 = '';
        }
        $res = Db::table('store_logistics_template')->alias('tem')
            ->join('store_post_mode mode','tem.mode_id = mode.id','LEFT')
            ->join('store_bis bis','tem.bis_id = bis.id','LEFT')
            ->where($where.'tem.status = 1'.$where1)
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