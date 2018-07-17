<?php
namespace app\index\controller;
use think\Controller;
class User extends Controller
{
    public function login()
    {
        return $this->fetch();
    }

    public function register(){
        if(request()->isPost()){
            $data = input('post.');

            if(!captcha_check($data['verifycode'])){
                $this->error('验证码不正确');
            }else{
                $this->success('验证码正确');
            }

        }
        return $this->fetch();
    }
}
