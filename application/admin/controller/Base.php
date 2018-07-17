<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Base extends Controller{

    public $admin_user_id;
    public function _initialize(){
        //判定用户是否登录
        $isLogin = $this->isLogin();
        if(!$isLogin){
            return $this->redirect(url('login/index'));
        }
    }

    public function isLogin(){
        //获取session值
        $admin_user_id = $this->getLoginUser();
        if($admin_user_id){
            return true;
        }
        return false;
    }

    public function getLoginUser(){
        if(!$this->admin_user_id){
            $this->admin_user_id = session('admin_user_id','','admin');
        }
        return $this->admin_user_id;
    }

}
