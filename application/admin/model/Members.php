<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Members extends Model{

    //获取所有会员信息
    public function getAllMembers($bis_id,$limit,$offset){
        if($bis_id >= 0){
            $where = [
                'mem.status'  => 1,
                'mem.bis_id'  => $bis_id
            ];
        }else{
            $where = [
                'mem.status'  => 1
            ];
        }

        $listorder = [
            'mem.id'  => 'desc'
        ];

        $res = Db::table('store_members')->alias('mem')->field('mem.id,mem.bis_id,mem.username,mem.nickname,mem.truename,mem.create_time,bis.bis_name,mem.team_status')
            ->join('store_bis bis','mem.bis_id = bis.id','LEFT')
            ->where($where)
            ->order($listorder)
            ->limit($offset,$limit)
            ->select();
        return $res;
    }

    //获取所有会员数量
    public function getAllMembersCount($bis_id){
        if($bis_id >= 0){
            $where = [
                'status'  => 1,
                'bis_id'  => $bis_id
            ];
        }else{
            $where = [
                'status'  => 1
            ];
        }

        $res = Db::table('store_members')->where($where)->count();
        return $res;
    }

    //根据id获取会员信息
    public function getMemberInfoById($id){
        $where = [
            'id'  => $id
        ];

        $res = Db::table('store_members')->where($where)->find();
        return $res;
    }

}

?>