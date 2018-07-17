<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller{

    public function index(){
        return $this->fetch();
    }

    public function test(){
        echo 1234555;
    }

    public function buy(){
        return $this->fetch();
    }

    public function detail(){
        return $this->fetch();
    }

    public function lists(){
        return $this->fetch();
    }

    public function orderlists(){
        return $this->fetch();
    }
}
