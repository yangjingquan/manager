<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class AdminUsers extends Model{

    //注册
    public function register($param){
        //获取参数
        $data = [
            'username' => $param['username'],
            'password' => md5($param['password']),
            'create_time' => date('Y-m-d H:i:s'),
        ];

        $res = Db::table('store_admin_users')->insert($data);
        return $res;
    }

    //更新数据
    public function updateById($data,$id){
        //allowField 过滤data数组中非数据表中的数据
        return $this->allowField(true)->save($data,['id' => $id]);
    }
}

?>