<?php
namespace app\bis\controller;
use app\api\controller\Image;
use think\Controller;
use think\Validate;
use think\Db;
class Register extends Controller
{
    public function index(){
        //获取一级城市数据
        $provinces = model('Province')->getProvinceInfo();
        //分类信息
        $cat_res = Db::table('store_category')->where('status = 1 and parent_id = 0')->select();
        return $this->fetch('',[
            'provinces'  => $provinces,
            'cat_res'  => $cat_res
        ]);
    }

    //商家用户注册
    public function register(){
        if(!request()->isPost()){
            $this->error('请求错误!');
        }

        //获取数据
        $param = input('post.');

        $validate = validate('Bis');

        if(!$validate->scene('register')->check($param)){
            $this->error($validate->getError());
        }

        //验证用户名唯一性
        $username = $param['username'];
        $user_res = Db::table('store_bis_admin_users')->where("username = '$username' and status != -1")->select();
        if($user_res){
            $this->error('该用户已存在!');
        }

        $res = model('Bis')->register($param);

        if($res){
            $con = [
                'bis_id'   => $res,
                'username'   => $param['username'],
                'password'   => md5($param['password']),
                'create_time'   => date('Y-m-d H:i:s')
            ];
            $user_info = Db::table('store_bis_admin_users')->insert($con);
            if($user_info){
                $this->success('注册成功!','login/index');
            }else{
                $this->error('注册失败!');
            }
        }else{
            $this->error('注册失败!');
        }

    }



}
