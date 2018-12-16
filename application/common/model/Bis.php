<?php
namespace app\common\model;
use think\Model;
use think\Db;
class Bis extends Model{

    //注册用户
    public function register($param){

        //获取当前地址
        $provinceId = $param['city_id'];
        $cityId = $param['se_city_id'];
        $address = $param['address'];
        $province = $this->getProvinceInfo($provinceId);
        $city = $this->getCityInfo($cityId);
        $address = $province.$city.$address;
        $positionRes = $this->execUrl($address);
        $positionArr = json_decode($positionRes,true);

        if(!empty($positionArr['pois'])){
            $location = $positionArr['pois'][0]['location'];
        }else{
            return false;
        }
        //获取参数
        $data = [
            'bis_name' => $param['bis_name'],
            'cat_id' => $param['category'],
            'brand' => $param['brand'],
            'leader' => $param['leader'],
            'citys' => $param['city_id'].','.$param['se_city_id'],
            'address' => $param['address'],
            'link_tel' => $param['link_tel'],
            'link_mobile' => $param['link_mobile'],
            'email' => $param['email'],
            'positions' => $location,
            'create_time' => date('Y-m-d H:i:s'),
        ];

        $res = Db::table('store_bis')->insertGetId($data);
        return $res;
    }

    //更新数据
    public function updateById($data,$id){
        //allowField 过滤data数组中非数据表中的数据
        return $this->allowField(true)->save($data,['id' => $id]);
    }

    public function execUrl($address){
        $url = "http://restapi.amap.com/v3/place/text?key=4c9ea4c4b4f719f7e69d625586f8c00d&keywords=".$address."&types=&city=&children=1&offset=20&page=1&extensions=all";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    //获取省级信息
    public function getProvinceInfo($provinceId){
        $res = Db::table('store_province')->where("id = '$provinceId'")->find();
        return $res['p_name'];
    }

    //获取市级信息
    public function getCityInfo($cityId){
        $res = Db::table('store_city')->where("id = '$cityId'")->find();
        return $res['c_name'];
    }


}

?>