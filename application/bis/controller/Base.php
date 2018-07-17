<?php
namespace app\bis\controller;
use think\Controller;
use think\Db;
class Base extends Controller{

    public $bis_user_id;
    public function _initialize(){
        //判定用户是否登录
        $isLogin = $this->isLogin();
        if(!$isLogin){
            return $this->redirect(url('login/index'));
        }
    }

    public function isLogin(){
        //获取session值
        $bis_user_id = $this->getLoginUser();
        if($bis_user_id){
            return true;
        }
        return false;
    }

    public function getLoginUser(){
        if(!$this->bis_user_id){
            $this->bis_user_id = session('bis_user_id','','bis');
        }
        return $this->bis_user_id;
    }

}
