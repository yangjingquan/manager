<?php
namespace app\admin\controller;
use think\Controller;
use think\Validate;
use app\api\controller\Image;
use think\Db;

class Bis extends Base {
    const PAGE_SIZE = 20;

    //店铺详情
    public function detail(){
        $bis_id = input('get.bis_id');
        //账号信息
        $res = Db::table('store_bis_admin_users')->field('id as u_id,username,password')->where('bis_id = '.$bis_id.' and status = 1')->select();
        //店铺信息
        $bis_res = Db::table('store_bis')->alias('bis')->field('bis.id,bis.bis_name,bis.brand,bis.jifen,bis.leader,bis.thumb,bis.link_tel,bis.config_type,bis.is_pintuan,bis.logistics_status,bis.appid,bis.secret,bis.mchid,bis.key,bis.fahuo_template_id,bis.acode,bis.is_ind_version,cat.id as cat_id,cat.cat_name')
            ->join('store_category cat','bis.cat_id = cat.id','LEFT')
            ->where('bis.id = '.$bis_id)
            ->find();
        //分类信息
        $cat_res = Db::table('store_category')->where('status = 1 and parent_id = 0')->select();
        return $this->fetch('',[
            'user_res'  => $res,
            'bis_res'  => $bis_res,
            'cat_res'  => $cat_res
         ]);
    }

    //餐饮店铺详情
    public function catDetail(){
        $bis_id = input('get.bis_id');
        //账号信息
        $res = Db::table('cy_bis_admin_users')->field('id as u_id,username,password')->where('bis_id = '.$bis_id.' and status = 1')->select();
        //店铺信息
        $bis_res = Db::table('cy_bis')->where('id = '.$bis_id)->find();

        return $this->fetch('catering/bis/index',[
            'user_res'  => $res,
            'bis_res'  => $bis_res
        ]);
    }

    //更新店铺信息
    public function save(){
        //获取参数
        $bis_id = input('post.id');
        $bis_name = input('post.bis_name');
        $jifen = input('post.jifen');
        $brand = input('post.brand');
        $leader = input('post.leader');
        $link_tel = input('post.link_tel');
        $category = input('post.category');
        $config_type = input('post.config_type');
        $group_type = input('post.group_type');
        $is_ind_version = input('post.is_ind_version');
        $logistics_status = input('post.logistics_status');
        $appid = input('post.appid');
        $secret = input('post.secret');
        $mchid = input('post.mchid');
        $key = input('post.key');
        $fahuo_template_id = input('post.fahuo_template_id');

        //设置更新字段
        $data = [
            'bis_name'  => $bis_name,
            'jifen'  => $jifen,
            'brand'  => $brand,
            'leader'  => $leader,
            'link_tel'  => $link_tel,
            'config_type'  => $config_type,
            'cat_id'  => $category,
            'is_pintuan'  => $group_type,
            'is_ind_version'  => $is_ind_version,
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
                'secret'  => $secret,
                'mchid'  => $mchid,
                'thumb'  => $thumb_url,
                'key'  => $key,
                'fahuo_template_id'  => $fahuo_template_id,
            ];
        }

        $res = Db::table('store_bis')->where('id = '.$bis_id)->update($data);

        $this->success("更新成功");
    }

    //修改餐饮店铺信息
    public function catSave(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        $param = input('post.');
        //设置添加到数据库的数据
        $data = [
            'bis_name' => $param['bis_name'],
            'brand' => $param['brand'],
            'leader' => $param['leader'],
            'link_tel' => $param['link_tel'],
            'link_mobile' => $param['link_mobile'],
            'email' => $param['email'],
            'intro' => $param['intro'],
            'scope' => $param['scope'],
            'min_price' => $param['min_price'],
            'lunch_box_fee' => $param['lunch_box_fee'],
            'distribution_fee' => $param['distribution_fee'],
            'business_time' => $param['business_time'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        $res = Db::table('cy_bis')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

    //商城商家列表
    public function index(){
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $count = Db::table('store_bis')->where('status >= 0')->count();
        $pages = ceil($count / $limit);
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name,citys,address,is_pintuan,status')
            ->where('status >= 0')
            ->limit($offset,self::PAGE_SIZE)
            ->order('id desc')
            ->select();

        $index = 0;
        foreach($bis_res as $val){
            $citys = $val['citys'];
            $temp_arr = explode(',',$citys);
            $province_id = $temp_arr[0];
            $city_id = $temp_arr[1];
            $province_info = Db::table('store_province')->field('p_name')->where('id = '.$province_id)->find();
            $city_info = Db::table('store_city')->field('c_name')->where('id = '.$city_id)->find();
            $province = $province_info['p_name'];
            $city = $city_info['c_name'];
            $bis_res[$index]['address_info'] = $province.$city.$val['address'];
            $index ++;
        }
        return $this->fetch('',[
            'bis_res'  => $bis_res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  => $count,
        ]);
    }

    //餐饮店铺列表
    public function list(){
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $count = Db::table('cy_bis')->where('status >= 0')->count();
        $pages = ceil($count / $limit);
        $bis_res = Db::table('cy_bis')->field('id as bis_id,bis_name,citys,address,business_time,status')
            ->where('status >= 0')
            ->limit($offset,self::PAGE_SIZE)
            ->order('id desc')
            ->select();

        $index = 0;
        foreach($bis_res as $val){
            $citys = $val['citys'];
            $temp_arr = explode(',',$citys);
            $province_id = $temp_arr[0];
            $city_id = $temp_arr[1];
            $province_info = Db::table('store_province')->field('p_name')->where('id = '.$province_id)->find();
            $city_info = Db::table('store_city')->field('c_name')->where('id = '.$city_id)->find();
            $province = $province_info['p_name'];
            $city = $city_info['c_name'];
            $bis_res[$index]['address_info'] = $province.$city.$val['address'];
            $index ++;
        }
        return $this->fetch('catering/bis/list',[
            'bis_res'  => $bis_res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  => $count,
        ]);
    }

    //商城--更改状态
    public function updateStatus(){
        //获取参数
        $bis_id = input('get.bis_id');
        $status = input('get.status');

        $data['status'] = $status;
        $res = Db::table('store_bis')->where('id = '.$bis_id)->update($data);
        $this->success("更新成功");
    }

    //餐饮--更改状态
    public function catUpdateStatus(){
        //获取参数
        $bis_id = input('get.bis_id');
        $status = input('get.status');

        $data['status'] = $status;
        $res = Db::table('cy_bis')->where('id = '.$bis_id)->update($data);
        $this->success("更新成功");
    }

    public function reg_index(){
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
                $this->success('注册成功!');
            }else{
                $this->error('注册失败!');
            }
        }else{
            $this->error('注册失败!');
        }

    }

    //获取小程序二维码(单用户版)
    public function getwxacode(){

        //获取参数
        $bis_id = input('post.bis_id');

        $bis_res = Db::table('store_home_info')->field('appid,secret')->where('id = 1')->find();
        $appid = $bis_res['appid'];
        $secret = $bis_res['secret'];

        if($appid == ''){
            return show(0,'fail','请先设置appid');
        }
        if($secret == ''){
            return show(0,'fail','请先设置secret');
        }

        //创建文件夹
        $upload_file_path = 'bis_wxcode/';
        if(!is_dir($upload_file_path)) {
            mkdir($upload_file_path,0777,true);
        }

        //获取access_token
        $access_token_json = $this->getAccessToken($appid,$secret);
        $arr = json_decode($access_token_json,true);
        $access_token = $arr['access_token'];

        //设置路径及二维码大小
        $path="pages/index/index?bis_id=".$bis_id;
        $width=430;

        $post_data='{"path":"'.$path.'","width":'.$width.'}';
        $url = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$access_token;

        $result = $this->api_notice_increment($url,$post_data);
        //设置图片名称
        $img_name = substr(date('Y'),2,2).date('m').date('d').$bis_id.'.png';

        //设置图片路径
        $img_path = $upload_file_path.$img_name;

        file_put_contents($img_path, $result);
        //将图片路径更新到商家表中
        $up_data['acode'] = $img_path;
        Db::table('store_bis')->where('id = '.$bis_id)->update($up_data);
        return show(1,'success');
    }

    //获取access_token
    public function getAccessToken($appid,$secret){

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    function api_notice_increment($url, $data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }else{
            return $tmpInfo;
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

    //店铺图片设置页面
    public function picDetail(){
        $bis_id = input('get.bis_id');
        $img_res = Db::table('cy_bis_images')->where('bis_id = '.$bis_id)->find();
        if(!$img_res){
            $img_res = [
                'logo_image'  => '',
                'env_image1'  => '',
                'env_image2'  => '',
                'env_image3'  => '',
                'env_image4'  => '',
                'env_image5'  => '',
                'env_image6'  => '',
                'qua_image1'  => '',
                'qua_image2'  => '',
                'qua_image3'  => '',
                'qua_image4'  => '',
                'qua_image5'  => '',
                'qua_image6'  => ''
            ];
        }

        return $this->fetch('catering/bis/img_index',[
            'bis_id'  => $bis_id,
            'img_res'  => $img_res
        ]);
    }

    //设置图片
    public function saveImgs(){
        $bis_id = input('post.bis_id');
        //上传图片相关
        $image = new Image();

        //设置图片
        if($_FILES['logo_image']['error'] == 0){
            $images_data['logo_image'] = $image->uploadS('logo_image','bisImgs');
            $images_data['logo_image'] = str_replace("\\", "/", $images_data['logo_image']);
        }
        if($_FILES['env_image1']['error'] == 0){
            $images_data['env_image1'] = $image->uploadS('env_image1','bisImgs');
            $images_data['env_image1'] = str_replace("\\", "/", $images_data['env_image1']);
        }
        if($_FILES['env_image2']['error'] == 0){
            $images_data['env_image2'] = $image->uploadS('env_image2','bisImgs');
            $images_data['env_image2'] = str_replace("\\", "/", $images_data['env_image2']);
        }
        if($_FILES['env_image3']['error'] == 0){
            $images_data['env_image3'] = $image->uploadS('env_image3','bisImgs');
            $images_data['env_image3'] = str_replace("\\", "/", $images_data['env_image3']);
        }
        if($_FILES['env_image4']['error'] == 0){
            $images_data['env_image4'] = $image->uploadS('env_image4','bisImgs');
            $images_data['env_image4'] = str_replace("\\", "/", $images_data['env_image4']);
        }
        if($_FILES['env_image5']['error'] == 0){
            $images_data['env_image5'] = $image->uploadS('env_image5','bisImgs');
            $images_data['env_image5'] = str_replace("\\", "/", $images_data['env_image5']);
        }
        if($_FILES['env_image6']['error'] == 0){
            $images_data['env_image6'] = $image->uploadS('env_image6','bisImgs');
            $images_data['env_image6'] = str_replace("\\", "/", $images_data['env_image6']);
        }
        if($_FILES['qua_image1']['error'] == 0){
            $images_data['qua_image1'] = $image->uploadS('qua_image1','bisImgs');
            $images_data['qua_image1'] = str_replace("\\", "/", $images_data['qua_image1']);
        }
        if($_FILES['qua_image2']['error'] == 0){
            $images_data['qua_image2'] = $image->uploadS('qua_image2','bisImgs');
            $images_data['qua_image2'] = str_replace("\\", "/", $images_data['qua_image2']);
        }
        if($_FILES['qua_image3']['error'] == 0){
            $images_data['qua_image3'] = $image->uploadS('qua_image3','bisImgs');
            $images_data['qua_image3'] = str_replace("\\", "/", $images_data['qua_image3']);
        }
        if($_FILES['qua_image4']['error'] == 0){
            $images_data['qua_image4'] = $image->uploadS('qua_image4','bisImgs');
            $images_data['qua_image4'] = str_replace("\\", "/", $images_data['qua_image4']);
        }
        if($_FILES['qua_image5']['error'] == 0){
            $images_data['qua_image5'] = $image->uploadS('qua_image5','bisImgs');
            $images_data['qua_image5'] = str_replace("\\", "/", $images_data['qua_image5']);
        }
        if($_FILES['qua_image6']['error'] == 0){
            $images_data['qua_image6'] = $image->uploadS('qua_image6','bisImgs');
            $images_data['qua_image6'] = str_replace("\\", "/", $images_data['qua_image6']);
        }

        $img_res = Db::table('cy_bis_images')->where('bis_id = '.$bis_id)->find();
        if(!$img_res){
            //设置添加的信息
            $insert_data = [
                'bis_id' => $bis_id,
                'logo_image' => !empty($images_data['logo_image']) ? $images_data['logo_image'] : '',
                'env_image1' => !empty($images_data['env_image1']) ? $images_data['env_image1'] : '',
                'env_image2' => !empty($images_data['env_image2']) ? $images_data['env_image2'] : '',
                'env_image3' => !empty($images_data['env_image3']) ? $images_data['env_image3'] : '',
                'env_image4' => !empty($images_data['env_image4']) ? $images_data['env_image4'] : '',
                'env_image5' => !empty($images_data['env_image5']) ? $images_data['env_image5'] : '',
                'env_image6' => !empty($images_data['env_image6']) ? $images_data['env_image6'] : '',
                'qua_image1' => !empty($images_data['qua_image1']) ? $images_data['qua_image1'] : '',
                'qua_image2' => !empty($images_data['qua_image2']) ? $images_data['qua_image2'] : '',
                'qua_image3' => !empty($images_data['qua_image3']) ? $images_data['qua_image3'] : '',
                'qua_image4' => !empty($images_data['qua_image4']) ? $images_data['qua_image4'] : '',
                'qua_image5' => !empty($images_data['qua_image5']) ? $images_data['qua_image5'] : '',
                'qua_image6' => !empty($images_data['qua_image6']) ? $images_data['qua_image6'] : '',
            ];

            $res = Db::table('cy_bis_images')->insert($insert_data);
        }else{
            //更新操作
            $res =  Db::table('cy_bis_images')->where('id = '.$img_res['id'])->update($images_data);
        }

        $this->success("更新成功!");
    }

}
