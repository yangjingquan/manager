<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Members extends Model{

    //获取所有会员信息
    public function getAllMembers($bis_id,$limit,$offset){
        $where = [
            'status'  => 1,
            'bis_id'  => $bis_id
        ];

        $listorder = [
            'id'  => 'desc'
        ];

        $res = Db::table('store_members')->where($where)->order($listorder)->limit($offset,$limit)->select();
        return $res;
    }

    //获取所有会员数量
    public function getAllMembersCount($bis_id){
        $where = [
            'status'  => 1,
            'bis_id'  => $bis_id
        ];


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