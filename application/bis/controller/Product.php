<?php
namespace app\bis\controller;
use think\Controller;
use app\api\controller\Image;
use think\Validate;
use think\Db;

class Product extends Base {

    //商品列表
    public function index(){
        $bis_id = session('bis_id','','bis');
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $pro_name = input('get.pro_name');

        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $pro_count = model('Products')->getAllProductCount($bis_id,$date_from,$date_to,$pro_name);
        //总页码
        $pages = ceil($pro_count / $limit);

        $pro_res = model('Products')->getAllProducts($bis_id,$limit, $offset,$date_from,$date_to,$pro_name);

        //获取该店家所传商品规格维数
        $config_type_res = Db::table('store_bis')->field('config_type')->where('id = '.$bis_id)->find();

        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'pages'  => $pages,
            'count'  => $pro_count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'pro_name'  => $pro_name,
            'config_type'  => $config_type_res['config_type']
        ]);
    }

    //积分商品列表
    public function jf_index(){
        $bis_id = session('bis_id','','bis');
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $pro_name = input('get.pro_name');

        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        $pro_count = model('Products')->getAllJfProductCount($bis_id,$date_from,$date_to,$pro_name);
        //总页码
        $pages = ceil($pro_count / $limit);

        $pro_res = model('Products')->getAllJfProducts($bis_id,$limit, $offset,$date_from,$date_to,$pro_name);

        //获取该店家所传商品规格维数
        $config_type_res = Db::table('store_bis')->field('config_type')->where('id = '.$bis_id)->find();

        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'pages'  => $pages,
            'count'  => $pro_count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'pro_name'  => $pro_name,
            'config_type'  => $config_type_res['config_type']
        ]);
    }

    //添加商品页面1
    public function add1(){
        $bis_id = session('bis_id','','bis');
        $year = date("Y");
        $first_category = model('Category')->getNormalFirstCategory();
        $defined_category = model('DefinedCategory')->getNormalFirstCategory($bis_id);
        $brand = model('Brand')->getNormalBrands($bis_id);
        //判断该店家是否开启拼团
        $pintuan_res = Db::table('store_bis')->field('is_pintuan')->where('id = '.$bis_id)->find();

        return $this->fetch('',[
            'year'  => $year,
            'first_category'  => $first_category,
            'defined_category'  => $defined_category,
            'brand'  => $brand,
            'is_pintuan'  => $pintuan_res['is_pintuan']
        ]);
    }

    //添加商品页面2
    public function add2(){
        $bis_id = session('bis_id','','bis');
        $year = date("Y");
        $first_category = model('Category')->getNormalFirstCategory();
        $defined_category = model('DefinedCategory')->getNormalFirstCategory($bis_id);
        $brand = model('Brand')->getNormalBrands($bis_id);
        //判断该店家是否开启拼团
        $pintuan_res = Db::table('store_bis')->field('is_pintuan')->where('id = '.$bis_id)->find();
        return $this->fetch('',[
            'year'  => $year,
            'first_category'  => $first_category,
            'defined_category'  => $defined_category,
            'brand'  => $brand,
            'is_pintuan'  => $pintuan_res['is_pintuan']
        ]);
    }

    //添加积分商品页面
    public function jf_add(){
        $bis_id = session('bis_id','','bis');
        $year = date("Y");
        $first_category = model('Category')->getNormalFirstCategory();
        $defined_category = model('DefinedCategory')->getNormalFirstCategory($bis_id);
        $brand = model('Brand')->getNormalBrands($bis_id);
        //判断该店家是否开启拼团
        $pintuan_res = Db::table('store_bis')->field('is_pintuan')->where('id = '.$bis_id)->find();

        return $this->fetch('',[
            'year'  => $year,
            'first_category'  => $first_category,
            'defined_category'  => $defined_category,
            'brand'  => $brand,
            'is_pintuan'  => $pintuan_res['is_pintuan']
        ]);
    }

    //编辑商品(一维规格)
    public function edit1(){
        //获取参数
        $id = input('get.id');
        $bis_id = session('bis_id','','bis');
        //获取该商品信息
        $pro_res = model('Products')->getProInfoById($id);
        $first_category = model('Category')->getNormalFirstCategory();
        $defined_category = model('DefinedCategory')->getNormalFirstCategory($bis_id);
        $brand = model('Brand')->getNormalBrands($bis_id);
        $pro_config = model('Products')->getConfigInfo($id);
        $pro_config_count = count($pro_config);
        //判断该店家是否开启拼团
        $pintuan_res = Db::table('store_bis')->field('is_pintuan')->where('id = '.$bis_id)->find();
        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'first_category'  => $first_category,
            'defined_category'  => $defined_category,
            'brand'  => $brand,
            'pro_config'  => $pro_config,
            'pro_config_count'  => $pro_config_count,
            'pro_id'  => $id,
            'is_pintuan'  => $pintuan_res['is_pintuan']

        ]);
    }

    //编辑商品(二维规格)
    public function edit2(){
        //获取参数
        $id = input('get.id');
        $bis_id = session('bis_id','','bis');
        //获取该商品信息
        $pro_res = model('Products')->getProInfoById($id);
//        dump($pro_res);
//        die;
        $first_category = model('Category')->getNormalFirstCategory();
        $defined_category = model('DefinedCategory')->getNormalFirstCategory($bis_id);
        $brand = model('Brand')->getNormalBrands($bis_id);
        $pro_config = model('Products')->getConfigInfo($id);
        $pro_config_count = count($pro_config);
        //判断该店家是否开启拼团
        $pintuan_res = Db::table('store_bis')->field('is_pintuan')->where('id = '.$bis_id)->find();
        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'first_category'  => $first_category,
            'defined_category'  => $defined_category,
            'brand'  => $brand,
            'pro_config'  => $pro_config,
            'pro_config_count'  => $pro_config_count,
            'pro_id'  => $id,
            'is_pintuan'  => $pintuan_res['is_pintuan']

        ]);
    }

    //编辑积分商品
    public function jf_edit(){
        //获取参数
        $id = input('get.id');
        $bis_id = session('bis_id','','bis');
        //获取该商品信息
        $pro_res = model('Products')->getProInfoById($id);
        $first_category = model('Category')->getNormalFirstCategory();
        $defined_category = model('DefinedCategory')->getNormalFirstCategory($bis_id);
        $brand = model('Brand')->getNormalBrands($bis_id);
        $pro_config = model('Products')->getConfigInfo($id);
        $pro_config_count = count($pro_config);

        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'first_category'  => $first_category,
            'defined_category'  => $defined_category,
            'brand'  => $brand,
            'pro_config'  => $pro_config,
            'pro_config_count'  => $pro_config_count,
            'pro_id'  => $id

        ]);
    }

    //添加商品
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');
        $bis_id = session('bis_id','','bis');

        //验证数据
        $validate = validate('Product');
        if(!$validate->scene('add')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();
        $config_error = $_FILES['config_image']['error'];
        $wx_config_error = $_FILES['wx_config_image']['error'];
        $is_pass = false;
        $wx_is_pass = false;
        foreach($config_error as $val){
            if($val == 0){
                $is_pass = true;
                break;
            }
        }
        foreach($wx_config_error as $val){
            if($val == 0){
                $wx_is_pass = true;
                break;
            }
        }

        if($is_pass){
            $config_data_temp = $image->uploadM('config_image','product');
            $config_data = array();
            foreach($config_data_temp as $val){
                $con_image = str_replace("\\", "/", $val);
                array_push($config_data,$con_image);
            }
        }else{
            $config_data = array();
        }

        if($wx_is_pass){
            $wx_config_data_temp = $image->uploadM('wx_config_image','product');
            $wx_config_data = array();
            foreach($wx_config_data_temp as $val){
                $con_image = str_replace("\\", "/", $val);
                array_push($wx_config_data,$con_image);
            }
        }else{
            $wx_config_data = array();
        }

        if($_FILES['image']['error'] == 0){
            $image_data = $image->uploadS('image','product');
            $image_data = str_replace("\\", "/", $image_data);
        }else{
            return $this->error('大图必须设置!');
        }

        if($_FILES['thumb']['error'] == 0){
            $thumb_data = $image->uploadS('thumb','product');
            $thumb_data = str_replace("\\", "/", $thumb_data);
        }else{
            return $this->error('小图必须设置!');
        }

        //设置添加到数据库的数据
        $product_data = [
            'bis_id' => $bis_id,
            'cat1_id' => $param['cat1_id'],
            'cat2_id' => $param['cat2_id'],
            'cat3_id' => '',
            'defined_cat1_id' => $param['d_cat1_id'],
            'defined_cat2_id' => $param['d_cat2_id'],
            'p_name' => $param['p_name'],
            'brand' => $param['brand'],
            'unit' => $param['unit'],
            'producing_area' => $param['producing_area'],
            'original_price' => $param['original_price'],
            'associator_discount' => $param['associator_discount'],
            'associator_price' => $param['associator_price'],
            'pintuan_price' => !empty($param['pintuan_price']) ? $param['pintuan_price'] : $param['associator_price'],
//            'vip_discount' => $param['vip_discount'],
//            'vip_price' => $param['vip_price'],
//            'vvip_discount' => $param['vvip_discount'],
//            'vvip_price' => $param['vvip_price'],
            'listorder' => $param['listorder'],
            'weight' => $param['weight'],
            'huohao' => $param['huohao'],
            'rate' => $param['rate'],
            'rec_rate' => $param['rec_rate'] / 100,
            'mem_product' => !empty($param['mem_product']) ? $param['mem_product'] : 0,
            'jifen' => $param['jifen'],
            'pintuan_count' => !empty($param['pintuan_count']) ? $param['pintuan_count'] : 2,
            'keywords' => $param['keywords'],
            'wx_introduce' => $param['wx_description'],
//            'nature' => $param['nature'],
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //普通数据添加到商品表中
        $p_res = model('Products')->add($product_data);

        //设置上传图片的内容
        if(!empty($config_data)){
            $config_image1 = !empty($config_data[0]) ? $config_data[0] : '';
            $config_image2 = !empty($config_data[1]) ? $config_data[1] : '';
            $config_image3 = !empty($config_data[2]) ? $config_data[2] : '';
            $config_image4 = !empty($config_data[3]) ? $config_data[3] : '';
        }else{
            $config_image1 = $config_image2 = $config_image3 = $config_image4 = '';
        }
        //设置小程序上传图片的内容
        if(!empty($wx_config_data)){
            $wx_config_image1 = !empty($wx_config_data[0]) ? $wx_config_data[0] : '';
            $wx_config_image2 = !empty($wx_config_data[1]) ? $wx_config_data[1] : '';
            $wx_config_image3 = !empty($wx_config_data[2]) ? $wx_config_data[2] : '';
            $wx_config_image4 = !empty($wx_config_data[3]) ? $wx_config_data[3] : '';
            $wx_config_image5 = !empty($wx_config_data[4]) ? $wx_config_data[4] : '';
            $wx_config_image6 = !empty($wx_config_data[5]) ? $wx_config_data[5] : '';
            $wx_config_image7 = !empty($wx_config_data[6]) ? $wx_config_data[6] : '';
            $wx_config_image8 = !empty($wx_config_data[7]) ? $wx_config_data[7] : '';
            $wx_config_image9 = !empty($wx_config_data[8]) ? $wx_config_data[8] : '';
            $wx_config_image10 = !empty($wx_config_data[9]) ? $wx_config_data[9] : '';
        }else{
            $wx_config_image1 = $wx_config_image2 = $wx_config_image3 = $wx_config_image4 = $wx_config_image5 = $wx_config_image6 = $wx_config_image7 = $wx_config_image8 = $wx_config_image9 = $wx_config_image10 = '';
        }

        $images_data = [
            'p_id'  => $p_res,
            'image'  =>  $image_data,
            'thumb'  => $thumb_data,
            'config_image1'  => $config_image1,
            'config_image2'  => $config_image2,
            'config_image3'  => $config_image3,
            'config_image4'  => $config_image4,
            'wx_config_image1'  => $wx_config_image1,
            'wx_config_image2'  => $wx_config_image2,
            'wx_config_image3'  => $wx_config_image3,
            'wx_config_image4'  => $wx_config_image4,
            'wx_config_image5'  => $wx_config_image5,
            'wx_config_image6'  => $wx_config_image6,
            'wx_config_image7'  => $wx_config_image7,
            'wx_config_image8'  => $wx_config_image8,
            'wx_config_image9'  => $wx_config_image9,
            'wx_config_image10'  => $wx_config_image10,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //添加图片数据到数据表
        $image_res = Db::table('store_pro_images')->insert($images_data);

        //设置商品配置信息
        $index = 0;
        if($param['config_type'] == 1){
            foreach($param['content1_name'] as $val){
                $p_config_data[] = [
                    'pro_id'  => $p_res,
                    'content1_name'  => $param['content1_name'][$index],
                    'con_content1'  => $param['con_content1'][$index],
                    'price'  => $param['unit_price'][$index],
                    'inventory'  => $param['inventory'][$index],
                    'group_price'  => !empty($param['group_price'][$index]) ? $param['group_price'][$index] : '0.00'
                ];
                $index ++;
            }
        }else{
            foreach($param['content1_name'] as $val){
                $p_config_data[] = [
                    'pro_id'  => $p_res,
                    'content1_name'  => $param['content1_name'][$index],
                    'con_content1'  => $param['con_content1'][$index],
                    'content2_name'  => $param['content2_name'][$index],
                    'con_content2'  => $param['con_content2'][$index],
                    'price'  => $param['unit_price'][$index],
                    'inventory'  => $param['inventory'][$index],
                    'group_price'  => !empty($param['group_price'][$index]) ? $param['group_price'][$index] : '0.00'
                ];
                $index ++;
            }
        }

        $config_res = Db::table('store_pro_config')->insertAll($p_config_data);

        if($p_res && $image_res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //添加积分商品
    public function jf_save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');
        $bis_id = session('bis_id','','bis');

        //验证数据
        $validate = validate('Product');
        if(!$validate->scene('add')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();
        $config_error = $_FILES['config_image']['error'];
        $wx_config_error = $_FILES['wx_config_image']['error'];
        $is_pass = false;
        $wx_is_pass = false;
        foreach($config_error as $val){
            if($val == 0){
                $is_pass = true;
                break;
            }
        }
        foreach($wx_config_error as $val){
            if($val == 0){
                $wx_is_pass = true;
                break;
            }
        }

        if($is_pass){
            $config_data_temp = $image->uploadM('config_image','product');
            $config_data = array();
            foreach($config_data_temp as $val){
                $con_image = str_replace("\\", "/", $val);
                array_push($config_data,$con_image);
            }
        }else{
            $config_data = array();
        }

        if($wx_is_pass){
            $wx_config_data_temp = $image->uploadM('wx_config_image','product');
            $wx_config_data = array();
            foreach($wx_config_data_temp as $val){
                $con_image = str_replace("\\", "/", $val);
                array_push($wx_config_data,$con_image);
            }
        }else{
            $wx_config_data = array();
        }

        if($_FILES['image']['error'] == 0){
            $image_data = $image->uploadS('image','product');
            $image_data = str_replace("\\", "/", $image_data);
        }else{
            return $this->error('大图必须设置!');
        }

        if($_FILES['thumb']['error'] == 0){
            $thumb_data = $image->uploadS('thumb','product');
            $thumb_data = str_replace("\\", "/", $thumb_data);
        }else{
            return $this->error('小图必须设置!');
        }

        //设置添加到数据库的数据
        $product_data = [
            'bis_id' => $bis_id,
            'cat1_id' => $param['cat1_id'],
            'cat2_id' => $param['cat2_id'],
            'cat3_id' => '',
            'defined_cat1_id' => $param['d_cat1_id'],
            'defined_cat2_id' => $param['d_cat2_id'],
            'p_name' => $param['p_name'],
            'brand' => $param['brand'],
            'unit' => $param['unit'],
            'producing_area' => $param['producing_area'],
            'original_price' => $param['original_price'],
            'associator_discount' => $param['associator_discount'],
            'associator_price' => $param['associator_price'],
            'ex_jifen' => $param['ex_jifen'],
            'listorder' => $param['listorder'],
            'weight' => $param['weight'],
            'huohao' => $param['huohao'],
            'rate' => $param['rate'],
            'keywords' => $param['keywords'],
            'wx_introduce' => $param['wx_description'],
            'is_jf_product' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //普通数据添加到商品表中
        $p_res = model('Products')->add($product_data);

        //设置上传图片的内容
        if(!empty($config_data)){
            $config_image1 = !empty($config_data[0]) ? $config_data[0] : '';
            $config_image2 = !empty($config_data[1]) ? $config_data[1] : '';
            $config_image3 = !empty($config_data[2]) ? $config_data[2] : '';
            $config_image4 = !empty($config_data[3]) ? $config_data[3] : '';
        }else{
            $config_image1 = $config_image2 = $config_image3 = $config_image4 = '';
        }
        //设置小程序上传图片的内容
        if(!empty($wx_config_data)){
            $wx_config_image1 = !empty($wx_config_data[0]) ? $wx_config_data[0] : '';
            $wx_config_image2 = !empty($wx_config_data[1]) ? $wx_config_data[1] : '';
            $wx_config_image3 = !empty($wx_config_data[2]) ? $wx_config_data[2] : '';
            $wx_config_image4 = !empty($wx_config_data[3]) ? $wx_config_data[3] : '';
            $wx_config_image5 = !empty($wx_config_data[4]) ? $wx_config_data[4] : '';
            $wx_config_image6 = !empty($wx_config_data[5]) ? $wx_config_data[5] : '';
            $wx_config_image7 = !empty($wx_config_data[6]) ? $wx_config_data[6] : '';
            $wx_config_image8 = !empty($wx_config_data[7]) ? $wx_config_data[7] : '';
            $wx_config_image9 = !empty($wx_config_data[8]) ? $wx_config_data[8] : '';
            $wx_config_image10 = !empty($wx_config_data[9]) ? $wx_config_data[9] : '';
        }else{
            $wx_config_image1 = $wx_config_image2 = $wx_config_image3 = $wx_config_image4 = $wx_config_image5 = $wx_config_image6 = $wx_config_image7 = $wx_config_image8 = $wx_config_image9 = $wx_config_image10 = '';
        }

        $images_data = [
            'p_id'  => $p_res,
            'image'  =>  $image_data,
            'thumb'  => $thumb_data,
            'config_image1'  => $config_image1,
            'config_image2'  => $config_image2,
            'config_image3'  => $config_image3,
            'config_image4'  => $config_image4,
            'wx_config_image1'  => $wx_config_image1,
            'wx_config_image2'  => $wx_config_image2,
            'wx_config_image3'  => $wx_config_image3,
            'wx_config_image4'  => $wx_config_image4,
            'wx_config_image5'  => $wx_config_image5,
            'wx_config_image6'  => $wx_config_image6,
            'wx_config_image7'  => $wx_config_image7,
            'wx_config_image8'  => $wx_config_image8,
            'wx_config_image9'  => $wx_config_image9,
            'wx_config_image10'  => $wx_config_image10,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //添加图片数据到数据表
        $image_res = Db::table('store_pro_images')->insert($images_data);

        //设置商品配置信息
        $index = 0;
        foreach($param['content1_name'] as $val){
            $p_config_data[] = [
                'pro_id'  => $p_res,
                'content1_name'  => $param['content1_name'][$index],
                'con_content1'  => $param['con_content1'][$index],
                'ex_jifen'  => $param['ex_jifen'][$index],
                'price'  => $param['unit_price'][$index],
                'inventory'  => $param['inventory'][$index],
                'group_price'  => !empty($param['group_price'][$index]) ? $param['group_price'][$index] : '0.00'
            ];
            $index ++;
        }

        $config_res = Db::table('store_pro_config')->insertAll($p_config_data);

        if($p_res && $image_res){
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
        $res = Db::table('store_products')->where('id = '.$id)->update($data);

        if($res){
            $this->success('更新状态成功!');
        }else{
            $this->error('更新状态失败!');
        }
    }

    //更改上架状态
    public function updateSaleStatus(){
        //获取参数
        $id = input('get.id');
        $on_sale = input('get.on_sale');
        $data['on_sale'] = $on_sale;
        $res = Db::table('store_products')->where('id = '.$id)->update($data);

        if($res){
            if($on_sale == 1){
                $this->success('上架成功!');
            }else{
                $this->success('下架成功!');
            }
        }else{
            $this->error('更新状态失败!');
        }
    }


    //更改推荐状态
    public function updateRecommendStatus(){
        //获取参数
        $id = input('get.id');
        $is_recommend = input('get.is_recommend');
        $data['is_recommend'] = $is_recommend;
        $data['update_time'] = date('Y-m-d H:i:s');
        $res = Db::table('store_products')->where('id = '.$id)->update($data);

        if($res){
            if($is_recommend == 1){
                $this->success('设置推荐成功!');
            }else{
                $this->success('取消推荐成功!');
            }
        }else{
            $this->error('更新状态失败!');
        }
    }

    //修改商品
    public function updateProduct(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //验证数据
        $validate = validate('Product');
        if(!$validate->scene('update')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();

        //设置图片
        if($_FILES['config_image1']['error'] == 0){
            $images_data['config_image1'] = $image->uploadS('config_image1','product');
            $images_data['config_image1'] = str_replace("\\", "/", $images_data['config_image1']);
        }
        if($_FILES['config_image2']['error'] == 0){
            $images_data['config_image2'] = $image->uploadS('config_image2','product');
            $images_data['config_image2'] = str_replace("\\", "/", $images_data['config_image2']);
        }
        if($_FILES['config_image3']['error'] == 0){
            $images_data['config_image3'] = $image->uploadS('config_image3','product');
            $images_data['config_image3'] = str_replace("\\", "/", $images_data['config_image3']);
        }
        if($_FILES['config_image4']['error'] == 0){
            $images_data['config_image4'] = $image->uploadS('config_image4','product');
            $images_data['config_image4'] = str_replace("\\", "/", $images_data['config_image4']);
        }
        if($_FILES['wx_config_image1']['error'] == 0){
            $images_data['wx_config_image1'] = $image->uploadS('wx_config_image1','product');
            $images_data['wx_config_image1'] = str_replace("\\", "/", $images_data['wx_config_image1']);
        }
        if($_FILES['wx_config_image2']['error'] == 0){
            $images_data['wx_config_image2'] = $image->uploadS('wx_config_image2','product');
            $images_data['wx_config_image2'] = str_replace("\\", "/", $images_data['wx_config_image2']);
        }
        if($_FILES['wx_config_image3']['error'] == 0){
            $images_data['wx_config_image3'] = $image->uploadS('wx_config_image3','product');
            $images_data['wx_config_image3'] = str_replace("\\", "/", $images_data['wx_config_image3']);
        }
        if($_FILES['wx_config_image4']['error'] == 0){
            $images_data['wx_config_image4'] = $image->uploadS('wx_config_image4','product');
            $images_data['wx_config_image4'] = str_replace("\\", "/", $images_data['wx_config_image4']);
        }
        if($_FILES['wx_config_image5']['error'] == 0){
            $images_data['wx_config_image5'] = $image->uploadS('wx_config_image5','product');
            $images_data['wx_config_image5'] = str_replace("\\", "/", $images_data['wx_config_image5']);
        }
        if($_FILES['wx_config_image6']['error'] == 0){
            $images_data['wx_config_image6'] = $image->uploadS('wx_config_image6','product');
            $images_data['wx_config_image6'] = str_replace("\\", "/", $images_data['wx_config_image6']);
        }
        if($_FILES['wx_config_image7']['error'] == 0){
            $images_data['wx_config_image7'] = $image->uploadS('wx_config_image7','product');
            $images_data['wx_config_image7'] = str_replace("\\", "/", $images_data['wx_config_image7']);
        }
        if($_FILES['wx_config_image8']['error'] == 0){
            $images_data['wx_config_image8'] = $image->uploadS('wx_config_image8','product');
            $images_data['wx_config_image8'] = str_replace("\\", "/", $images_data['wx_config_image8']);
        }
        if($_FILES['wx_config_image9']['error'] == 0){
            $images_data['wx_config_image9'] = $image->uploadS('wx_config_image9','product');
            $images_data['wx_config_image9'] = str_replace("\\", "/", $images_data['wx_config_image9']);
        }
        if($_FILES['wx_config_image10']['error'] == 0){
            $images_data['wx_config_image10'] = $image->uploadS('wx_config_image10','product');
            $images_data['wx_config_image10'] = str_replace("\\", "/", $images_data['wx_config_image10']);
        }
        if($_FILES['image']['error'] == 0){
            $images_data['image'] = $image->uploadS('image','product');
            $images_data['image'] = str_replace("\\", "/", $images_data['image']);
        }
        if($_FILES['thumb']['error'] == 0){
            $images_data['thumb'] = $image->uploadS('thumb','product');
            $images_data['thumb'] = str_replace("\\", "/", $images_data['thumb']);
        }

        //设置更新的数据
        $product_data = [
            'cat1_id' => $param['cat1_id'],
            'cat2_id' => $param['cat2_id'],
            'cat3_id' => '',
            'defined_cat1_id' => $param['d_cat1_id'],
            'defined_cat2_id' => $param['d_cat2_id'],
            'p_name' => $param['p_name'],
            'brand' => $param['brand'],
            'unit' => $param['unit'],
            'producing_area' => $param['producing_area'],
            'original_price' => $param['original_price'],
            'associator_discount' => $param['associator_discount'],
            'associator_price' => $param['associator_price'],
            'pintuan_price' => !empty($param['pintuan_price']) ? $param['pintuan_price'] : $param['associator_price'],
//            'vip_discount' => $param['vip_discount'],
//            'vip_price' => $param['vip_price'],
//            'vvip_discount' => $param['vvip_discount'],
//            'vvip_price' => $param['vvip_price'],
            'listorder' => $param['listorder'],
            'weight' => $param['weight'],
            'huohao' => $param['huohao'],
            'jifen' => $param['jifen'],
            'rate' => $param['rate'],
            'rec_rate' => $param['rec_rate'] / 100,
            'mem_product' => !empty($param['mem_product']) ? $param['mem_product'] : 0,
            'pintuan_count' => $param['pintuan_count'] ? $param['pintuan_count'] : 2,
            'keywords' => $param['keywords'],
            'wx_introduce' => $param['wx_description'],
//            'nature' => $param['nature'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        //更新商品表
        $p_res = model('Products')->where('id = '.$param['pro_id'])->update($product_data);

        $images_data['update_time'] = date('Y-m-d H:i:s');

        //更新图片表
        $image_res = Db::table('store_pro_images')->where('p_id = '.$param['pro_id'])->update($images_data);


        $this->success("修改成功!");
    }

    //修改积分商品
    public function jf_updateProduct(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //验证数据
        $validate = validate('Product');
        if(!$validate->scene('update')->check($param)){
            $this->error($validate->getError());
        }

        //上传图片相关
        $image = new Image();

        //设置图片
        if($_FILES['config_image1']['error'] == 0){
            $images_data['config_image1'] = $image->uploadS('config_image1','product');
            $images_data['config_image1'] = str_replace("\\", "/", $images_data['config_image1']);
        }
        if($_FILES['config_image2']['error'] == 0){
            $images_data['config_image2'] = $image->uploadS('config_image2','product');
            $images_data['config_image2'] = str_replace("\\", "/", $images_data['config_image2']);
        }
        if($_FILES['config_image3']['error'] == 0){
            $images_data['config_image3'] = $image->uploadS('config_image3','product');
            $images_data['config_image3'] = str_replace("\\", "/", $images_data['config_image3']);
        }
        if($_FILES['config_image4']['error'] == 0){
            $images_data['config_image4'] = $image->uploadS('config_image4','product');
            $images_data['config_image4'] = str_replace("\\", "/", $images_data['config_image4']);
        }
        if($_FILES['wx_config_image1']['error'] == 0){
            $images_data['wx_config_image1'] = $image->uploadS('wx_config_image1','product');
            $images_data['wx_config_image1'] = str_replace("\\", "/", $images_data['wx_config_image1']);
        }
        if($_FILES['wx_config_image2']['error'] == 0){
            $images_data['wx_config_image2'] = $image->uploadS('wx_config_image2','product');
            $images_data['wx_config_image2'] = str_replace("\\", "/", $images_data['wx_config_image2']);
        }
        if($_FILES['wx_config_image3']['error'] == 0){
            $images_data['wx_config_image3'] = $image->uploadS('wx_config_image3','product');
            $images_data['wx_config_image3'] = str_replace("\\", "/", $images_data['wx_config_image3']);
        }
        if($_FILES['wx_config_image4']['error'] == 0){
            $images_data['wx_config_image4'] = $image->uploadS('wx_config_image4','product');
            $images_data['wx_config_image4'] = str_replace("\\", "/", $images_data['wx_config_image4']);
        }
        if($_FILES['wx_config_image5']['error'] == 0){
            $images_data['wx_config_image5'] = $image->uploadS('wx_config_image5','product');
            $images_data['wx_config_image5'] = str_replace("\\", "/", $images_data['wx_config_image5']);
        }
        if($_FILES['wx_config_image6']['error'] == 0){
            $images_data['wx_config_image6'] = $image->uploadS('wx_config_image6','product');
            $images_data['wx_config_image6'] = str_replace("\\", "/", $images_data['wx_config_image6']);
        }
        if($_FILES['wx_config_image7']['error'] == 0){
            $images_data['wx_config_image7'] = $image->uploadS('wx_config_image7','product');
            $images_data['wx_config_image7'] = str_replace("\\", "/", $images_data['wx_config_image7']);
        }
        if($_FILES['wx_config_image8']['error'] == 0){
            $images_data['wx_config_image8'] = $image->uploadS('wx_config_image8','product');
            $images_data['wx_config_image8'] = str_replace("\\", "/", $images_data['wx_config_image8']);
        }
        if($_FILES['wx_config_image9']['error'] == 0){
            $images_data['wx_config_image9'] = $image->uploadS('wx_config_image9','product');
            $images_data['wx_config_image9'] = str_replace("\\", "/", $images_data['wx_config_image9']);
        }
        if($_FILES['wx_config_image10']['error'] == 0){
            $images_data['wx_config_image10'] = $image->uploadS('wx_config_image10','product');
            $images_data['wx_config_image10'] = str_replace("\\", "/", $images_data['wx_config_image10']);
        }
        if($_FILES['image']['error'] == 0){
            $images_data['image'] = $image->uploadS('image','product');
            $images_data['image'] = str_replace("\\", "/", $images_data['image']);
        }
        if($_FILES['thumb']['error'] == 0){
            $images_data['thumb'] = $image->uploadS('thumb','product');
            $images_data['thumb'] = str_replace("\\", "/", $images_data['thumb']);
        }

        //设置更新的数据
        $product_data = [
            'cat1_id' => $param['cat1_id'],
            'cat2_id' => $param['cat2_id'],
            'cat3_id' => '',
            'defined_cat1_id' => $param['d_cat1_id'],
            'defined_cat2_id' => $param['d_cat2_id'],
            'p_name' => $param['p_name'],
            'brand' => $param['brand'],
            'unit' => $param['unit'],
            'producing_area' => $param['producing_area'],
            'original_price' => $param['original_price'],
            'associator_discount' => $param['associator_discount'],
            'associator_price' => $param['associator_price'],
            'ex_jifen' => $param['ex_jifen'],
            'listorder' => $param['listorder'],
            'weight' => $param['weight'],
            'huohao' => $param['huohao'],
            'rate' => $param['rate'],
            'keywords' => $param['keywords'],
            'wx_introduce' => $param['wx_description'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        //更新商品表
        $p_res = model('Products')->where('id = '.$param['pro_id'])->update($product_data);

        $images_data['update_time'] = date('Y-m-d H:i:s');

        //更新图片表
        $image_res = Db::table('store_pro_images')->where('p_id = '.$param['pro_id'])->update($images_data);


        $this->success("修改成功!");
    }

    //更新配置信息1
    public function updateConfigInfo1(){
        //获取参数
        $param = input('post.');

        $data = [
            'content1_name'   => $param['content1_name'],
            'con_content1'   => $param['con_content1'],
            'price'   => $param['unit_price'],
            'inventory'   => $param['inventory'],
            'group_price'   => !empty($param['group_price']) ? $param['group_price'] : $param['unit_price'],
        ];

        $res = Db::table('store_pro_config')->where('id = '.$param['config_id'])->update($data);
        if($res){
            return show(1,'success','edit1?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //更新配置信息2
    public function updateConfigInfo2(){
        //获取参数
        $param = input('post.');

        $data = [
            'content1_name'   => $param['content1_name'],
            'con_content1'   => $param['con_content1'],
            'content2_name'   => $param['content2_name'],
            'con_content2'   => $param['con_content2'],
            'price'   => $param['unit_price'],
            'inventory'   => $param['inventory'],
            'group_price'   => !empty($param['group_price']) ? $param['group_price'] : $param['unit_price'],
        ];

        $res = Db::table('store_pro_config')->where('id = '.$param['config_id'])->update($data);
        if($res){
            return show(1,'success','edit2?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //更新积分商品配置信息
    public function jf_updateConfigInfo(){
        //获取参数
        $param = input('post.');

        $data = [
            'content1_name'   => $param['content1_name'],
            'con_content1'   => $param['con_content1'],
            'price'   => $param['unit_price'],
            'inventory'   => $param['inventory'],
            'ex_jifen'   => $param['ex_jifen'],
        ];

        $res = Db::table('store_pro_config')->where('id = '.$param['config_id'])->update($data);
        if($res){
            return show(1,'success','jf_edit?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //添加配置信息1
    public function addConfigInfo1(){
        //获取参数
        $param = input('post.');

        $data = [
            'pro_id'   => $param['pro_id'],
            'content1_name'   => $param['content1_name'],
            'con_content1'   => $param['con_content1'],
            'price'   => $param['unit_price'],
            'inventory'   => $param['inventory'],
            'group_price'   => !empty($param['group_price']) ? $param['group_price'] : $param['unit_price'],
        ];

        $res = Db::table('store_pro_config')->insert($data);
        if($res){
            return show(1,'success','edit1?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //添加配置信息2
    public function addConfigInfo2(){
        //获取参数
        $param = input('post.');

        $data = [
            'pro_id'   => $param['pro_id'],
            'content1_name'   => $param['content1_name'],
            'con_content1'   => $param['con_content1'],
            'content2_name'   => $param['content2_name'],
            'con_content2'   => $param['con_content2'],
            'price'   => $param['unit_price'],
            'inventory'   => $param['inventory'],
            'group_price'   => !empty($param['group_price']) ? $param['group_price'] : $param['unit_price'],
        ];

        $res = Db::table('store_pro_config')->insert($data);
        if($res){
            return show(1,'success','edit2?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //添加积分商品配置信息
    public function jf_addConfigInfo(){
        //获取参数
        $param = input('post.');

        $data = [
            'pro_id'   => $param['pro_id'],
            'content1_name'   => $param['content1_name'],
            'con_content1'   => $param['con_content1'],
            'price'   => $param['unit_price'],
            'inventory'   => $param['inventory'],
            'ex_jifen'   => $param['ex_jifen'],
        ];

        $res = Db::table('store_pro_config')->insert($data);
        if($res){
            return show(1,'success','jf_edit?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //删除配置信息1
    public function removeConfigInfo1(){
        //获取参数
        $param = input('post.');

        $data = [
            'status'  => 0
        ];

        $res = Db::table('store_pro_config')->where('id = '.$param['config_id'])->update($data);
        if($res){
            return show(1,'success','edit1?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //删除配置信息2
    public function removeConfigInfo2(){
        //获取参数
        $param = input('post.');

        $data = [
            'status'  => 0
        ];

        $res = Db::table('store_pro_config')->where('id = '.$param['config_id'])->update($data);
        if($res){
            return show(1,'success','edit2?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //删除积分商品配置信息
    public function jf_removeConfigInfo(){
        //获取参数
        $param = input('post.');

        $data = [
            'status'  => 0
        ];

        $res = Db::table('store_pro_config')->where('id = '.$param['config_id'])->update($data);
        if($res){
            return show(1,'success','jf_edit?id='.$param['pro_id']);
        }else{
            return show(0,'error');
        }
    }

    //获取指定商品下的配置信息
    public function getConfigInfoByProid(){
        //获取参数
        $id = input('post.id');
        $pro_config = model('Products')->getConfigInfo($id);

        if($pro_config){
            return show(1,'success',$pro_config);
        }else{
            return show(0,'error');
        }


    }



}
