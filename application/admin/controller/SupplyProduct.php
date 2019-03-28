<?php
namespace app\admin\controller;
use think\Controller;
use app\api\controller\Image;
use think\Db;

class SupplyProduct extends Base {

    const PAGE_SIZE = 20;

    //商品列表
    public function index(){
        $date_from = input('get.date_from');
        $date_to = input('get.date_to');
        $pro_name = input('get.pro_name');

        $current_page = input('get.current_page',1,'intval');
        $limit = self::PAGE_SIZE;
        $offset = ($current_page - 1) * $limit;
        $pro_count = model('SupplyProducts')->getAllProductCount($date_from,$date_to,$pro_name);
        //总页码
        $pages = ceil($pro_count / $limit);

        $pro_res = model('SupplyProducts')->getAllProducts($limit, $offset,$date_from,$date_to,$pro_name);

        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'pages'  => $pages,
            'count'  => $pro_count,
            'current_page'  => $current_page,
            'date_from'  => $date_from,
            'date_to'  => $date_to,
            'pro_name'  => $pro_name,
        ]);
    }

    //添加商品页面
    public function add(){
        $year = date("Y");
        $first_category = model('Category')->getNormalFirstCategory();

        return $this->fetch('',[
            'year'  => $year,
            'first_category'  => $first_category,
        ]);
    }

    //添加商品
    public function save(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }

        //获取提交的数据
        $param = input('post.');

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
                $con_image = self::IMG_URL.str_replace("\\", "/", $val);
                array_push($config_data,$con_image);
            }
        }else{
            $config_data = array();
        }

        if($wx_is_pass){
            $wx_config_data_temp = $image->uploadM('wx_config_image','product');
            $wx_config_data = array();
            foreach($wx_config_data_temp as $val){
                $con_image = self::IMG_URL.str_replace("\\", "/", $val);
                array_push($wx_config_data,$con_image);
            }
        }else{
            $wx_config_data = array();
        }
        $imageArr = array();
        if($_FILES['image']['error'] == 0){
            $image_data = $image->uploadS('image','product');
            $imageArr['image'] = self::IMG_URL.str_replace("\\", "/", $image_data);
        }else{
            return $this->error('大图必须设置!');
        }

        if($_FILES['thumb']['error'] == 0){
            $thumb_data = $image->uploadS('thumb','product');
            $imageArr['thumb'] = self::IMG_URL.str_replace("\\", "/", $thumb_data);
        }else{
            return $this->error('小图必须设置!');
        }

        //设置添加到数据库的数据
        $product_data = [
            'cat1_id' => $param['cat1_id'],
            'cat2_id' => $param['cat2_id'],
            'p_name' => $param['p_name'],
            'unit' => $param['unit'],
            'producing_area' => $param['producing_area'],
            'original_price' => $param['original_price'],
            'associator_discount' => $param['associator_discount'],
            'associator_price' => $param['associator_price'],
            'pintuan_price' => !empty($param['pintuan_price']) ? $param['pintuan_price'] : $param['associator_price'],
            'listorder' => $param['listorder'],
            'weight' => $param['weight'],
            'huohao' => $param['huohao'],
            'rate' => $param['rate'],
            'rec_rate' => $param['rec_rate'] / 100,
            'supply_price' => $param['supply_price'],
            'pintuan_count' => !empty($param['pintuan_count']) ? $param['pintuan_count'] : 2,
            'keywords' => $param['keywords'],
            'wx_introduce' => $param['wx_description'],
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];

        //普通数据添加到商品表中
        $p_res = model('Products')->add($product_data);

        //设置上传图片的内容
        if(!empty($config_data)){
            $imageArr['config_image1'] = !empty($config_data[0]) ? $config_data[0] : '';
            $imageArr['config_image2'] = !empty($config_data[1]) ? $config_data[1] : '';
            $imageArr['config_image3'] = !empty($config_data[2]) ? $config_data[2] : '';
            $imageArr['config_image4'] = !empty($config_data[3]) ? $config_data[3] : '';
        }else{
            $imageArr['config_image1'] = $imageArr['config_image2'] = $imageArr['config_image3'] = $imageArr['config_image4'] = '';
        }
        //设置小程序上传图片的内容
        if(!empty($wx_config_data)){
            $imageArr['wx_config_image1'] = !empty($wx_config_data[0]) ? $wx_config_data[0] : '';
            $imageArr['wx_config_image2'] = !empty($wx_config_data[1]) ? $wx_config_data[1] : '';
            $imageArr['wx_config_image3'] = !empty($wx_config_data[2]) ? $wx_config_data[2] : '';
            $imageArr['wx_config_image4'] = !empty($wx_config_data[3]) ? $wx_config_data[3] : '';
            $imageArr['wx_config_image5'] = !empty($wx_config_data[4]) ? $wx_config_data[4] : '';
            $imageArr['wx_config_image6'] = !empty($wx_config_data[5]) ? $wx_config_data[5] : '';
            $imageArr['wx_config_image7'] = !empty($wx_config_data[6]) ? $wx_config_data[6] : '';
            $imageArr['wx_config_image8'] = !empty($wx_config_data[7]) ? $wx_config_data[7] : '';
            $imageArr['wx_config_image9'] = !empty($wx_config_data[8]) ? $wx_config_data[8] : '';
            $imageArr['wx_config_image10'] = !empty($wx_config_data[9]) ? $wx_config_data[9] : '';
        }else{
            $imageArr['wx_config_image1'] = $imageArr['wx_config_image2'] = $imageArr['wx_config_image3'] = $imageArr['wx_config_image4'] = $imageArr['wx_config_image5'] = $imageArr['wx_config_image6'] = $imageArr['wx_config_image7'] = $imageArr['wx_config_image8'] = $imageArr['wx_config_image9'] = $imageArr['wx_config_image10'] = '';
        }

        $product_data['images'] = json_encode($imageArr);

        //设置商品配置信息
        $index = 0;
        $inventory = 0;
        foreach($param['content1_name'] as $val){
            $p_config_data[] = [
                'content1_name'  => $param['content1_name'][$index],
                'con_content1'  => $param['con_content1'][$index],
                'price'  => $param['unit_price'][$index],
                'inventory'  => $param['inventory'][$index],
                'group_price'  => !empty($param['group_price'][$index]) ? $param['group_price'][$index] : '0.00'
            ];
            $inventory = $inventory + $param['inventory'][$index];
            $index ++;
        }
        $product_data['configs'] = json_encode($p_config_data);
        $product_data['inventory'] = $inventory;

        //执行添加
        $p_res = model('SupplyProducts')->add($product_data);

        if($p_res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //编辑商品(一维规格)
    public function edit(){
        //获取参数
        $id = input('get.id');
        //获取该商品信息
        $pro_res = model('SupplyProducts')->getProInfoById($id);

        $first_category = model('Category')->getNormalFirstCategory();
        $pro_config = $pro_res['configs'];
        $pro_config_count = count($pro_config);

        return $this->fetch('',[
            'pro_res'  => $pro_res,
            'first_category'  => $first_category,
            'pro_config'  => $pro_config,
            'pro_config_count'  => $pro_config_count,
            'pro_id'  => $id,
            'no_img_url'  => self::NO_IMG_URL
        ]);
    }

    //更改状态
    public function updateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('store_supply_products')->where('id = '.$id)->update($data);

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
        $res = Db::table('store_supply_products')->where('id = '.$id)->update($data);

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
        $res = Db::table('store_supply_products')->where('id = '.$id)->update($data);

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

        $pro_res = model('SupplyProducts')->getProInfoById($param['pro_id']);
        $images_data = $pro_res['images'];
        //上传图片相关
        $image = new Image();
//        $images_data['image'] = $images_data['thumb'] = $images_data['config_image1'] = $images_data['config_image2'] = $images_data['config_image3'] = $images_data['config_image4'] = $images_data['wx_config_image1'] = $images_data['wx_config_image2'] = $images_data['wx_config_image3'] = $images_data['wx_config_image4'] = $images_data['wx_config_image5'] = $images_data['wx_config_image6'] = $images_data['wx_config_image7'] = $images_data['wx_config_image8'] = $images_data['wx_config_image9'] = $images_data['wx_config_image10'] = '';
        //设置图片
        if($_FILES['config_image1']['error'] == 0){
            $images_data['config_image1'] = $image->uploadS('config_image1','product');
            $images_data['config_image1'] = self::IMG_URL.str_replace("\\", "/", $images_data['config_image1']);
        }
        if($_FILES['config_image2']['error'] == 0){
            $images_data['config_image2'] = $image->uploadS('config_image2','product');
            $images_data['config_image2'] = self::IMG_URL.str_replace("\\", "/", $images_data['config_image2']);
        }
        if($_FILES['config_image3']['error'] == 0){
            $images_data['config_image3'] = $image->uploadS('config_image3','product');
            $images_data['config_image3'] = self::IMG_URL.str_replace("\\", "/", $images_data['config_image3']);
        }
        if($_FILES['config_image4']['error'] == 0){
            $images_data['config_image4'] = $image->uploadS('config_image4','product');
            $images_data['config_image4'] = self::IMG_URL.str_replace("\\", "/", $images_data['config_image4']);
        }
        if($_FILES['wx_config_image1']['error'] == 0){
            $images_data['wx_config_image1'] = $image->uploadS('wx_config_image1','product');
            $images_data['wx_config_image1'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image1']);
        }
        if($_FILES['wx_config_image2']['error'] == 0){
            $images_data['wx_config_image2'] = $image->uploadS('wx_config_image2','product');
            $images_data['wx_config_image2'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image2']);
        }
        if($_FILES['wx_config_image3']['error'] == 0){
            $images_data['wx_config_image3'] = $image->uploadS('wx_config_image3','product');
            $images_data['wx_config_image3'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image3']);
        }
        if($_FILES['wx_config_image4']['error'] == 0){
            $images_data['wx_config_image4'] = $image->uploadS('wx_config_image4','product');
            $images_data['wx_config_image4'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image4']);
        }
        if($_FILES['wx_config_image5']['error'] == 0){
            $images_data['wx_config_image5'] = $image->uploadS('wx_config_image5','product');
            $images_data['wx_config_image5'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image5']);
        }
        if($_FILES['wx_config_image6']['error'] == 0){
            $images_data['wx_config_image6'] = $image->uploadS('wx_config_image6','product');
            $images_data['wx_config_image6'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image6']);
        }
        if($_FILES['wx_config_image7']['error'] == 0){
            $images_data['wx_config_image7'] = $image->uploadS('wx_config_image7','product');
            $images_data['wx_config_image7'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image7']);
        }
        if($_FILES['wx_config_image8']['error'] == 0){
            $images_data['wx_config_image8'] = $image->uploadS('wx_config_image8','product');
            $images_data['wx_config_image8'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image8']);
        }
        if($_FILES['wx_config_image9']['error'] == 0){
            $images_data['wx_config_image9'] = $image->uploadS('wx_config_image9','product');
            $images_data['wx_config_image9'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image9']);
        }
        if($_FILES['wx_config_image10']['error'] == 0){
            $images_data['wx_config_image10'] = $image->uploadS('wx_config_image10','product');
            $images_data['wx_config_image10'] = self::IMG_URL.str_replace("\\", "/", $images_data['wx_config_image10']);
        }
        if($_FILES['image']['error'] == 0){
            $images_data['image'] = $image->uploadS('image','product');
            $images_data['image'] = self::IMG_URL.str_replace("\\", "/", $images_data['image']);
        }
        if($_FILES['thumb']['error'] == 0){
            $images_data['thumb'] = $image->uploadS('thumb','product');
            $images_data['thumb'] = self::IMG_URL.str_replace("\\", "/", $images_data['thumb']);
        }


        //设置更新的数据
        $product_data = [
            'cat1_id' => $param['cat1_id'],
            'cat2_id' => $param['cat2_id'],
            'p_name' => $param['p_name'],
            'unit' => $param['unit'],
            'producing_area' => $param['producing_area'],
            'original_price' => $param['original_price'],
            'associator_discount' => $param['associator_discount'],
            'associator_price' => $param['associator_price'],
            'pintuan_price' => !empty($param['pintuan_price']) ? $param['pintuan_price'] : $param['associator_price'],
            'listorder' => $param['listorder'],
            'weight' => $param['weight'],
            'huohao' => $param['huohao'],
            'rate' => $param['rate'],
            'supply_price' => $param['supply_price'],
            'rec_rate' => $param['rec_rate'] / 100,
            'pintuan_count' => $param['pintuan_count'] ? $param['pintuan_count'] : 2,
            'keywords' => $param['keywords'],
            'wx_introduce' => $param['wx_description'],
            'update_time' => date('Y-m-d H:i:s')
        ];

        $product_data['images'] = json_encode($images_data);
        //设置商品配置信息
        $index = 0;
        $inventory = 0;
        $p_config_data = array();
        foreach($param['content1_name'] as $val){
            $p_config_data[] = [
                'content1_name'  => $param['content1_name'][$index],
                'con_content1'  => $param['con_content1'][$index],
                'price'  => $param['unit_price'][$index],
                'inventory'  => $param['inventory'][$index],
                'group_price'  => !empty($param['group_price'][$index]) ? $param['group_price'][$index] : '0.00'
            ];
            $inventory = $inventory + $param['inventory'][$index];
            $index ++;
        }
        $product_data['configs'] = json_encode($p_config_data);
        $product_data['inventory'] = $inventory;

        //更新商品表
        $p_res = Db::table('store_supply_products')->where('id = '.$param['pro_id'])->update($product_data);

        $this->success("修改成功!");
    }

}
