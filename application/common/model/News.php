<?php
namespace app\common\model;
use think\Model;
use think\Db;
class News extends Model{

    //获取所有会员信息
    public function getNewsList($bis_id,$limit,$offset, $type, $keyword){
        $where = "news.status = 1 and news.bis_id = $bis_id";
        if($type != ''){
            if($type == 'id'){
                $where .= " and news.id = ".$keyword;
            }elseif($type == 'theme'){
                $where .= " and news.theme like '%$keyword%'";
            }else{
                $where .= " and news.content like '%$keyword%'";
            }

        }

        $listorder = [
            'news.update_time'  => 'desc',
        ];

        $res = Db::table('store_news')->alias('news')->field('news.id as news_id,news.theme,news.update_time,news.is_recommend,u.username,news.is_top')
                ->join('store_bis_admin_users u','news.publisher = u.id','LEFT')
                ->where($where)
                ->order($listorder)
                ->limit($offset,$limit)
                ->select();

        return $res;
    }

    //获取所有会员数量
    public function getNewsCount($bis_id, $type, $keyword){
        $where = "news.status = 1 and news.bis_id = $bis_id";
        if($type != ''){
            if($type == 'id'){
                $where .= " and news.id = ".$keyword;
            }elseif($type == 'theme'){
                $where .= " and news.theme like '%$keyword%'";
            }else{
                $where .= " and news.content like '%$keyword%'";
            }

        }

        $res = Db::table('store_news')->alias('news')
            ->join('store_bis_admin_users u','news.publisher = u.id','LEFT')
            ->where($where)
            ->count();
        return $res;
    }


}

?>