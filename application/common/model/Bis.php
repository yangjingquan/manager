<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Bis extends Model{

    //注册用户
    public function register($param){

        //获取参数
        $data = [
            'bis_name' => $param['bis_name'],
            'cat_id' => $param['category'],
            'brand' => $param['brand'],
            'leader' => $param['leader'],
            'citys' => $param['city_id'].','.$param['se_city_id'],
            'address' => $param['address'],
            'link_tel' => $param['link_tel'],
            'link_mobile' => $param['link_mobile'],
            'email' => $param['email'],
            'create_time' => date('Y-m-d H:i:s'),
        ];

        $res = Db::table('store_bis')->insertGetId($data);
        return $res;
    }

    //更新数据
    public function updateById($data,$id){
        //allowField 过滤data数组中非数据表中的数据
        return $this->allowField(true)->save($data,['id' => $id]);
    }


}

?>