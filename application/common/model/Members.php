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

    //获取新增会员数量
    public function getNewMembersCount($bis_id,$date_from,$date_to){
        $date  = date('Y-m-d 00:00:00');
        $next_date  = date("Y-m-d 00:00:00",strtotime("+1 day"));

        $where = " status <> -1";
        $default_sql = " and create_time >= '$date' and create_time < '$next_date'";

        if($bis_id){
            if(!$date_from && !$date_to){
                $where .= $default_sql." and bis_id = ".$bis_id ;
            }else{
                if($date_from){
                    $where .= " and create_time >= '$date_from' and bis_id = ".$bis_id;
                }
                if($date_to){
                    $where .= " and create_time < '$date_to' and bis_id = ".$bis_id;
                }
            }
        }else{
            if(!$date_from && !$date_to){
                $where .= $default_sql." and bis_id = ".$bis_id ;
            }else{
                if($date_from){
                    $where .= " and create_time >= '$date_from'";
                }
                if($date_to){
                    $where .= " and create_time < '$date_to'";
                }
            }
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