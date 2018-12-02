<?php
namespace app\admin\controller;
use think\Controller;
use think\Validate;
use think\Db;

class Activity extends Base {
    const PAGE_SIZE = 20;
    //活动列表
    public function index(){
        $bis_id = input('get.bis_id',0);
        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Activitys')->getActivitysCount($bis_id);

        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Activitys')->getActivitys($bis_id,$limit,$offset);
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/activity/index',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'bis_res'  => $bis_res,
            'bis_id'  => $bis_id,
        ]);
    }

    //添加活动页面
    public function add(){
        //获取店铺信息
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name')->where('status = 1')->select();
        return $this->fetch('catering/activity/add',['bis_res' => $bis_res]);
    }

    //编辑活动
    public function edit(){
        //获取参数
        $id = input('get.id');
        $bis_id = input('get.bis_id');
        //获取该分类信息
        $res = Db::table('cy_activitys')->where('id = '.$id)->find();

        return $this->fetch('catering/activity/edit',[
            'activity'  => $res
        ]);
    }

    //添加活动
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => $param['bis_id'],
            'type' => $param['act_type'],
            'activity_name' => $param['activity_name'],
            'max' => $param['max'],
            'lose' => $param['lose'],
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        $res = model('Activitys')->add($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }


    //更改状态
    public function updateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('cy_activitys')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改活动
    public function updateActivity(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        $param = input('post.');
        //设置添加到数据库的数据
        $data = [
            'activity_name' => $param['activity_name'],
            'type' => $param['act_type'],
            'max' => $param['max'],
            'lose' => $param['lose'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_activitys')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }
}
