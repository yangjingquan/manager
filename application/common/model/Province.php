<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Province extends Model{

    //获取省级信息
    public function getProvinceInfo(){
        $order = [
            'id'  => 'asc'
        ];
        $where['status'] = 1;
        return Db::table('store_province')->where($where)->order($order)->select();
    }
}

?>