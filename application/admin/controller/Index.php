<?php
namespace app\admin\controller;
use think\Controller;
class Index extends Base {

    //系统首页
    public function index(){
        return $this->fetch();
    }

    public function welcome(){
        return $this->fetch();
    }

    public function test(){
        echo session('admin_user_ids','','index');
    }
}
