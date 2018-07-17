<?php
namespace app\bis\controller;
use think\Controller;
use app\api\controller\Image;
use think\Validate;
use think\Db;

class Logistics extends Controller {

    //模板列表
    public function index(){
        $bis_id = session('bis_id','','bis');
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
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'post_mode'  => $post_mode,
            'post_mode_res'  => $post_mode_res,
            'ykj_price'  => $ykj_price,
            'transport_type'  => $transport_type,
        ]);
    }

    //添加模板页面
    public function add(){
        //获取省级信息
        $province_res = model('Province')->getProvinceInfo();
        //获取快递种类
        $post_mode_res = model('Logistics')->getPostMode();

        return $this->fetch('',[
            'province_res'  => $province_res,
            'post_mode_res'  => $post_mode_res
        ]);
    }

    //添加模板
    public function save(){
        $bis_id = session('bis_id','','bis');
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
        $bis_id = session('bis_id','','bis');
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

    //切换运费模式
    public function changeTransportType(){
        //获取参数
        $bis_id = session('bis_id','','bis');
        $transport_type = input('get.transport_type');
        $ykj_price = input('get.ykj_price');

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
            $this->success('切换成功!');
        }else{
            $this->error('切换失败!');
        }
    }

}
