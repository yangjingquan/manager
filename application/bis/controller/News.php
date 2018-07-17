<?php
namespace app\bis\controller;
use app\api\controller\Image;
use think\Controller;
use think\Validate;
use think\Db;
class News extends Base {

    //新闻列表页面
    public function index(){
        //接收参数
        $type = input('get.select_type');
        $keyword = input('get.keyword');
        $bis_id = session('bis_id','','bis');
        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;
        //总数量
        $count = model('News')->getNewsCount($bis_id, $type, $keyword);
        //总页码
        $pages = ceil($count / $limit);
        //结果集
        $res = model('News')->getNewsList($bis_id, $limit, $offset, $type, $keyword);
        return $this->fetch('',[
            'res'  => $res,
            'pages'  => $pages,
            'current_page'  => $current_page,
            'count'  => $count,
            'type'  => $type,
            'keyword'  => $keyword,
        ]);
    }

    //添加新闻页面
    public function add(){
        return $this->fetch();
    }

    //添加新闻
    public function save(){
        //获取参数
        $param = input('post.');
        //验证数据
        $validate = validate('News');
        if(!$validate->scene('add')->check($param)){
            $this->error($validate->getError());
        }
        $data = [
            'bis_id'        => session('bis_id','','bis'),
            'theme'         => $param['theme'],
            'publisher'     => session('bis_user_id','','bis'),
            'content'       => $param['content'],
            'is_recommend'  => !empty($param['is_recommend']) ? 1 : 0,
            'create_time'   => date('Y-m-d H:i:s'),
        ];
        $res = Db::table('store_news')->insert($data);

        if($res){
            return $this->success('添加成功!');
        }else{
            return $this->error('添加失败!');
        }
    }

    //根据id查询新闻信息
    public function edit(){
        //获取数据
        $id = input('get.news_id');
        $res = Db::table('store_news')->where('id = '.$id)->find();
        return $this->fetch('',[
            'res'  => $res
        ]);
    }

    //修改新闻
    public function update(){
        //获取参数
        $param = input('post.');
        //验证数据
        $validate = validate('News');
        if(!$validate->scene('update')->check($param)){
            $this->error($validate->getError());
        }

        $news_id = $param['news_id'];
        $data = [
            'theme'         => $param['theme'],
            'publisher'     => session('bis_user_id','','bis'),
            'content'       => $param['content'],
            'is_recommend'  => !empty($param['is_recommend']) ? 1 : 0,
            'update_time'   => date('Y-m-d H:i:s'),
        ];
        $res = Db::table('store_news')->where('id = '.$news_id)->update($data);

        if($res){
            return $this->success('修改成功!','news/index');
        }else{
            return $this->error('修改失败!');
        }
    }

    //修改置顶状态
    public function updateIsTop(){
        $id = input('get.id');
        $data['is_top'] = input('get.is_top');

        $res = Db::table('store_news')->where('id = '.$id)->update($data);

        if($res){
            return $this->success('修改置顶状态成功!');
        }else{
            return $this->error('修改置顶状态失败!');
        }
    }

    //修改推荐状态
    public function updateIsRecommend(){
        $id = input('get.id');
        $data['is_recommend'] = input('get.is_recommend');

        $res = Db::table('store_news')->where('id = '.$id)->update($data);

        if($res){
            return $this->success('修改推荐状态成功!');
        }else{
            return $this->error('修改推荐状态失败!');
        }
    }

}
