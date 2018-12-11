<?php
namespace app\bis\controller;
use think\Controller;
use think\Validate;
use app\api\controller\Image;
use think\Db;

class Bis extends Base {

    //店铺信息
    public function bis_setting(){
        $bis_id = session('bis_id','','bis');
        $user_id = session('bis_user_id','','bis');
        $mem_res = Db::table('store_bis_admin_users')->field('id,username,password')->where('id = '.$user_id)->find();
        $res = Db::table('store_bis')->where('id = '.$bis_id)->find();

        $citys = $res['citys'];
        $citysArr = explode(',',$citys);
        $provinceId = $citysArr[0];
        $cityId = $citysArr[1];

        //省级信息
        $provinces = model('Province')->getProvinceInfo();
        //市级信息
        $citys = model('Province')->getCitysById($provinceId);

        //分类信息
        $cat_res = Db::table('store_category')->where('status = 1 and parent_id = 0')->select();

        return $this->fetch('',[
            'bis_res'  => $res,
            'mem_res'  => $mem_res,
            'cat_res'  => $cat_res,
            'provinces'  => $provinces,
            'citys'  => $citys,
            'province_id'  => $provinceId,
            'city_id'  => $cityId,
        ]);
    }

    //更新店铺信息
    public function save(){
        //获取参数
        $bis_id = input('post.id');
        $bis_name = input('post.bis_name');
        $citys = input('post.city_id').','.input('post.se_city_id');
        $address = input('post.address');
        $jifen = input('post.jifen');
        $brand = input('post.brand');
        $leader = input('post.leader');
        $link_tel = input('post.link_tel');
        $category = input('post.category');
        $config_type = input('post.config_type');
        $group_type = input('post.group_type');
        $logistics_status = input('post.logistics_status');
        $appid = input('post.appid');
        $secret = input('post.secret');
        $mchid = input('post.mchid');
        $key = input('post.key');
        $fahuo_template_id = input('post.fahuo_template_id');

        //设置更新字段
        $data = [
            'bis_name'  => $bis_name,
            'citys'  => $citys,
            'address'  => $address,
            'jifen'  => $jifen,
            'brand'  => $brand,
            'leader'  => $leader,
            'link_tel'  => $link_tel,
            'config_type'  => $config_type,
            'cat_id'  => $category,
            'is_pintuan'  => $group_type,
            'logistics_status'  => $logistics_status,
            'appid'  => $appid,
            'secret'  => $secret,
            'mchid'  => $mchid,
            'key'  => $key,
            'fahuo_template_id'  => $fahuo_template_id,
        ];

        //上传图片相关
        $image = new Image();
        if($_FILES['thumb']['error'] == 0){
            $thumb_url = $image->uploadS('thumb','bis');
            $thumb_url = str_replace("\\", "/", $thumb_url);
            $data = [
                'bis_name'  => $bis_name,
                'jifen'  => $jifen,
                'brand'  => $brand,
                'leader'  => $leader,
                'link_tel'  => $link_tel,
                'config_type'  => $config_type,
                'cat_id'  => $category,
                'is_pintuan'  => $group_type,
                'logistics_status'  => $logistics_status,
                'appid'  => $appid,
                'mchid'  => $mchid,
                'thumb'  => $thumb_url,
                'key'  => $key,
                'fahuo_template_id'  => $fahuo_template_id,
            ];
        }

        $res = Db::table('store_bis')->where('id = '.$bis_id)->update($data);

        if($res){
            $this->success("更新成功");
        }else{
            $this->error('更新失败');
        }

    }

    //修改密码
    public function editPwd(){
        //获取参数
        $user_id = input('post.user_id');
        $pwd = input('post.password');
        $data['password'] = md5($pwd);
        $res = Db::table('store_bis_admin_users')->where('id = '.$user_id)->update($data);
        return show(1,'success');
    }
}
