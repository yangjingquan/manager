<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Base extends Controller{
    //本地图片地址
    const IMG_URL = 'http://store.spring.com/';
//    const IMG_URL = 'http://cp.58canyin.com:88/';
    //本地默认图片
    const NO_IMG_URL = 'http://store.spring.com/uploads/images/no_img.png';
    //服务器
//    const NO_IMG_URL = 'http://cp.58canyin.com:88/uploads/images/no_img.png';

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
