<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Login extends Controller {

    //登录
    public function index(){

        if(request()->isPost()){
            //登录的逻辑
            //获取相关的数据
            $param = input('post.');
            //通过用户名 获取 用户相关信息
            $admin_res = Db::table('store_admin_users')->where("username = '$param[username]'")->find();

            if(!$admin_res || $admin_res['status'] != 1){
                $this->error('该用户不存在或已失效');
            }

            if($admin_res['password'] != md5($param['password'])){
                $this->error('密码不正确');
            }
            $update_data['last_login_time'] = date('Y-m-d H:i:s');
            Db::table('store_admin_users')->where('id = '.$admin_res['id'])->update($update_data);

            // 保存用户信息
            session('admin_user_id',$admin_res['id'],'admin');
            session('admin_username',$admin_res['username'],'admin');

            return $this->success('登录成功',url('index/index',['admin_name'=>$admin_res['username']]));
        }else{
            $admin_user_id = session('admin_user_id','','admin');
            $admin_username = session('admin_username','','admin');

            if($admin_user_id){
                return $this->redirect(url('index/index',['admin_name'=>$admin_username]));
            }
            return $this->fetch();
        }
    }

    //退出登录
    public function logout(){
        //清除session
        session(null,'admin');

        echo json_encode(array(
            'statuscode'   =>  1,
        ));
        exit;
    }

}
