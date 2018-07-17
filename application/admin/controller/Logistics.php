<?php
namespace app\admin\controller;
use think\Controller;
use app\api\controller\Image;
use think\Validate;
use think\Db;

class Logistics extends Controller {

    //模板列表
    public function index(){
        $bis_id = input('get.bis_id',0,'intval');
        $current_page = input('get.current_page',1,'intval');
        $post_mode = input('get.post_mode','全部');

        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('Logistics')->getTemplateCount($bis_id,$post_mode);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('Logistics')->getTemplates($bis_id,$limit,$offset,$post_mode);

        //获取快递种类
        $post_mode_res = model('Logistics')->getPostMode();
        //获取运费种类
        $transport_res = Db::table('store_bis')->field('transport_type,ykj_price')->where('id = '.$bis_id)->find();
        $ykj_price = $transport_res['ykj_price'];
        $transport_type = $transport_res['transport_type'];
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('transport_type = 1 and logistics_status = 1 and status = 1')->select();
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'post_mode'  => $post_mode,
            'post_mode_res'  => $post_mode_res,
            'ykj_price'  => $ykj_price,
            'transport_type'  => $transport_type,
            'bis_id'  => $bis_id,
            'bis_res'  => $bis_res,
        ]);
    }

    //店铺模式列表
    public function type_index(){
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = Db::table('store_bis')->where('logistics_status = 1 and status = 1')->count();
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = Db::table('store_bis')->field('id as bis_id,bis_name,transport_type,ykj_price')
            ->where('logistics_status = 1 and status = 1')
            ->limit($offset,$limit)
            ->order('id desc')
            ->select();

        $index = 0;
        foreach($res as $val){
            $res[$index]['transport_text'] = $val['transport_type'] == 1 ? '模板模式' : '一口价模式';
            $index ++;
        }
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page
        ]);
    }

    //添加模板页面
    public function add(){
        //获取省级信息
        $province_res = model('Province')->getProvinceInfo();
        //获取快递种类
        $post_mode_res = model('Logistics')->getPostMode();
        //获取店铺信息
        $bis_res = Db::table('store_bis')->field('id as bis_id,bis_name')->where('transport_type = 1 and logistics_status = 1 and status = 1')->select();
        return $this->fetch('',[
            'province_res'  => $province_res,
            'post_mode_res'  => $post_mode_res,
            'bis_res'  => $bis_res
        ]);
    }

    //添加模板
    public function save(){
        $bis_id = input('post.bis_id');
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'bis_id' => $bis_id,
            'mode_id' => $param['post_mode'],
            'province' => $param['province'],
            'first_heavy' => $param['first_heavy'],
            'continue_heavy' => $param['continue_heavy']
        ];

        //验证重复
        $where = "bis_id = ".$bis_id." and mode_id = ".$param['post_mode']." and province like '%".$param['province']."%'" ." and status = 1";
        $l_res = Db::table('store_logistics_template')->where($where)->select();
        if($l_res){
            $this->error('该模板已经存在!');
        }
        $res = model('Logistics')->add($data);

        if($res){
            $this->success("新增成功");
        }else{
            $this->error('新增失败');
        }
    }

    //编辑模板
    public function edit(){
        //获取参数
        $id = input('get.id');
        $bis_id = session('bis_id','','bis');
        //获取省级信息
        $province_res = model('Province')->getProvinceInfo();
        //获取快递种类
        $post_mode_res = model('Logistics')->getPostMode();
        //获取模板信息
        $tem_res = model('Logistics')->getTemRes($id);
        return $this->fetch('',[
            'province_res'  => $province_res,
            'post_mode_res'  => $post_mode_res,
            'tem_res'  => $tem_res
        ]);
    }

    //修改模板
    public function updateTemplate(){
        if(!request()->isPost()){
            $this->error('请求方式错误!');
        }
        //获取提交的数据
        $param = input('post.');

        //设置添加到数据库的数据
        $data = [
            'first_heavy' => $param['first_heavy'],
            'continue_heavy' => $param['continue_heavy']
        ];

        $res = Db::table('store_logistics_template')->where('id = '.$param['id'])->update($data);

        if($res){
            $this->success("修改成功");
        }else{
            $this->error('修改失败');
        }
    }

    //更改状态
    public function updateStatus(){
        //获取参数
        $id = input('get.id');
        $status = input('get.status');
        $data['status'] = $status;
        $res = Db::table('store_logistics_template')->where('id = '.$id)->update($data);

        if($res){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

    //切换运费模式页面
    public function type_edit(){
        //获取参数
        $bis_id = input('get.id');
        $res = Db::table('store_bis')->field('id,transport_type,ykj_price')->where('id = '.$bis_id)->find();
        return $this->fetch('',[
            'res'  => $res
        ]);
    }

    //切换运费模式
    public function changeTransportType(){
        //获取参数
        $bis_id = input('post.bis_id');
        $transport_type = input('post.transport_type');
        $ykj_price = input('post.ykj_price');

        //设置更新数据
        if($transport_type == 1){
            $data = [
                'transport_type' => 1
            ];
        }else{
            $data = [
                'transport_type' => 2,
                'ykj_price' => $ykj_price
            ];
        }

        $res = Db::table('store_bis')->where('id = '.$bis_id)->update($data);

        if($res){
            $this->success('更新成功!');
        }else{
            $this->error('更新失败!');
        }
    }

}
