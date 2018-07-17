<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Team extends Controller {

    //加入组织
    public function join_team(){
        //接收参数
        $new_id = input('post.id');

        //获取该用户的推荐用户
        $mem_res = Db::table('store_members')->field('rec_id,team_status')->where('id = '.$new_id)->find();
        $rec_id = $mem_res['rec_id'];

        if($mem_res['team_status'] == 1){
            return show(1,'success',$_SERVER['HTTP_REFERER']);
        }

        //生成一条以new_id为首的新数据
        $new_data = [
            'a'  => $new_id,
            'rec_id'  => $rec_id,
            'create_time'  => date('Y-m-d H:i:s')
        ];
        Db::table('store_teams')->insert($new_data);

        //更新会员表team_status字段
        $mem_data['team_status'] = 1;
        Db::table('store_members')->where('id = '.$new_id)->update($mem_data);

        if($rec_id == 1){
            return show(1,'success',$_SERVER['HTTP_REFERER']);
        }else{
            //判断推荐人是否已加入团队
            $mem_res = Db::table('store_members')->field('team_status')->where('id = '.$rec_id)->find();
            $team_status = $mem_res['team_status'];
            if($team_status == 0){
                return show(1,'success',$_SERVER['HTTP_REFERER']);
            }else{
                //判断推荐人位置 两种情况
                //①.推荐人在b1或b2位置
                $rec_place_where1_1 = "b1 = ".$rec_id.' and status = 0';
                $rec_place_where1_2 = "b2 = ".$rec_id.' and status = 0';
                $rec_place_res1_1 = Db::table('store_teams')->field('id as team_id,b1,b2,c1,c2,c3,c4')->where($rec_place_where1_1)->order('id desc')->find();
                $rec_place_res1_2 = Db::table('store_teams')->field('id as team_id,b1,b2,c1,c2,c3,c4')->where($rec_place_where1_2)->order('id desc')->find();
                if($rec_place_res1_1 || $rec_place_res1_2){
                    if($rec_place_res1_1){
                        $team_id = $rec_place_res1_1['team_id'];
                        echo $rec_place_res1_1['c1'];
                        if($rec_place_res1_1['c1'] == ''){
                            $this->updateC1Value($new_id,$team_id);

                            //更新以b1为首的单条记录
                            $this->updateTopWithB1NoTeam($new_id,$rec_place_res1_1['b1']);
                        }else{
                            //判断c2是否为空
                            if($rec_place_res1_1['c2'] == ''){
                                //更新c2的值
                                $this->updateC2Value($new_id,$team_id);

                                //验证id为$team_id的此条数据,当前b2,c3,c4是否都有值
                                //如果都有,则以a为首的7个人构成一个团队,更新status,a下来,作为c2的b1,并更新以b1为首的单条记录;
                                //如果没有都满,a位置保留,更新以b1为首的单条记录
                                $this->checkC2About($team_id,$new_id,$rec_place_res1_1);
                            }else{
                                $res = Db::table('store_teams')->field('id as team_id,b1,b2,c1,c2,c3,c4')->where('a = '.$rec_id.' and status = 0')->order('id desc')->find();
                                $team_id = $res['team_id'];
                                if($res['c1'] == ''){
                                    $this->updateC1Value($new_id,$team_id);
                                    //更新以b1为首的单条记录
                                    $this->updateTopWithB1NoTeam($new_id,$res['b1']);
                                }elseif($res['c2'] == ''){
                                    //更新c2的值
                                    $this->updateC2Value($new_id,$team_id);
                                    //验证id为$team_id的此条数据,当前b2,c3,c4是否都有值
                                    //如果都有,则以a为首的7个人构成一个团队,更新status,a下来,作为c2的b1,并更新以b1为首的单条记录;
                                    //如果没有都满,a位置保留,更新以b1为首的单条记录
                                    $this->checkC2About($team_id,$new_id,$res);
                                }elseif($res['c3'] == ''){
                                    //更新c3的值
                                    $this->updateC3Value($new_id,$team_id);

                                    //更新以b2为首的单条记录
                                    $this->updateTopWithB2NoTeam($new_id,$res['b2']);
                                }else{
                                    //更新c4的值
                                    $this->updateC4Value($new_id,$team_id);

                                    //更新c4相关
                                    $this->checkC4About($team_id,$new_id,$res);
                                }
                            }
                        }
                    }else{
                        $team_id = $rec_place_res1_2['team_id'];
                        if($rec_place_res1_2['c3'] == ''){
                            //更新c3的值
                            $this->updateC3Value($new_id,$team_id);

                            //更新以b2为首的单条记录
                            $res = Db::table('store_teams')->field('b2')->where('id = '.$team_id)->find();
                            $this->updateTopWithB2NoTeam($new_id,$res['b2']);
                        }else{
                            //更新c4的值
                            $this->updateC4Value($new_id,$team_id);

                            //验证id为$team_id的此条数据,当前b1,c1,c2是否都有值
                            //如果都有,则以a为首的7个人构成一个团队,更新status,a下来,作为c4的b1,并更新以b2为首的单条记录;
                            //如果没有都满,a位置保留,更新以b2为首的单条记录
                            //更新c4相关
                            $this->checkC4About1($team_id,$new_id,$rec_place_res1_2);
                        }
                    }
                }else{
                    //②.推荐人在a位置
                    $rec_place_where2 = "a = ".$rec_id.' and status = 0';
                    $rec_place_res2 = Db::table('store_teams')->field('id as team_id,b1,b2,c1,c2,c3,c4')->where($rec_place_where2)->order('id desc')->find();
                    $team_id = $rec_place_res2['team_id'];
                    $rec_place_where['id'] = $team_id;

                    if($rec_place_res2['b1'] == ''){
                        $rec_place_data['b1'] = $new_id;
                        Db::table('store_teams')->where($rec_place_where)->update($rec_place_data);
                    }elseif($rec_place_res2['b2'] == ''){
                        $rec_place_data['b2'] = $new_id;
                        Db::table('store_teams')->where($rec_place_where)->update($rec_place_data);
                    }elseif($rec_place_res2['c1'] == ''){

                        $this->updateC1Value($new_id,$team_id);
                        //更新以b1为首的单条记录
                        $this->updateTopWithB1NoTeam($new_id,$rec_place_res2['b1']);
                    }elseif($rec_place_res2['c2'] == ''){

                        //更新c2的值
                        $this->updateC2Value($new_id,$team_id);
                        //验证id为$team_id的此条数据,当前b2,c3,c4是否都有值
                        //如果都有,则以a为首的7个人构成一个团队,更新status,a下来,作为c2的b1,并更新以b1为首的单条记录;
                        //如果没有都满,a位置保留,更新以b1为首的单条记录
                        $this->checkC2About($team_id,$new_id,$rec_place_res2);
                    }elseif($rec_place_res2['c3'] == ''){

                        //更新c3的值
                        $this->updateC3Value($new_id,$team_id);

                        //更新以b2为首的单条记录
                        $res = Db::table('store_teams')->field('b2')->where('id = '.$team_id)->find();
                        $this->updateTopWithB2NoTeam($new_id,$res['b2']);

                    }else{
                        //更新c4的值
                        $this->updateC4Value($new_id,$team_id);

                        //验证id为$team_id的此条数据,当前b1,c1,c2是否都有值
                        //如果都有,则以a为首的7个人构成一个团队,更新status,a下来,作为c4的b1,并更新以b2为首的单条记录;
                        //如果没有都满,a位置保留,更新以b2为首的单条记录

                        //更新c4相关
                        $this->checkC4About1($team_id,$new_id,$rec_place_res2);
                    }
                }
            }
            return show(1,'success',$_SERVER['HTTP_REFERER']);
        }

    }

    //更新c1处的值
    public function updateC1Value($new_id,$team_id){
        $data['c1'] = $new_id;
        $where['id'] = $team_id;
        Db::table('store_teams')->where($where)->update($data);
    }

    //更新以b1为首的单条记录(未达成team版)
    public function updateTopWithB1NoTeam($new_id,$b1){
        $res = Db::table('store_teams')->field('b1,b2')->where('a = '.$b1.' and status = 0')->order('id desc')->find();
        if($res['b1'] == ''){
            $data['b1'] = $new_id;
        }else{
            $data['b2'] = $new_id;
        }
        Db::table('store_teams')->where('a = '.$b1.' and status = 0')->update($data);
    }

    //更新以b1为首的单条记录(达成team版)
    public function updateTopWithB1Team($new_id,$b1,$a){
        $data['b2'] = $new_id;
        $data['c3'] = $a;
        Db::table('store_teams')->where('a = '.$b1.' and status = 0')->update($data);

        //更新推荐人的推荐人直销收入字段
        $res = Db::table('store_members')->field('rec_id')->where('id = '.$a)->find();
        $res1 = Db::table('store_members')->field('ketixian,direct_selling_income')->where('id = '.$res['rec_id'])->find();
        $mem_data['direct_selling_income'] = number_format($res1['direct_selling_income'] + 9.99,2,".","");
        $mem_data['ketixian'] = number_format($res1['ketixian'] + 9.99,2,".","");
        Db::table('store_members')->where('id = '.$res['rec_id'])->update($mem_data);

        //生成一条以$a为首的记录
        $new_data = [
            'a'  => $a,
            'rec_id' => $res['rec_id'],
            'create_time'  => date('Y-m-d H:i:s')
        ];
        Db::table('store_teams')->insert($new_data);

    }

    //更新c2处的值
    public function updateC2Value($new_id,$team_id){
        $data['c2'] = $new_id;
        $where['id'] = $team_id;
        Db::table('store_teams')->where($where)->update($data);
    }

    //更新c2相关
    public function checkC2About($team_id,$new_id,$rec_place_res){
        $res = Db::table('store_teams')->field('a,b2,c3,c4')->where('id = '.$team_id)->find();
        if($res['b2'] != '' && $res['c3'] != '' && $res['c4'] != ''){
            //达成team
            //更新status
            $this->updateStatus($team_id);

            //a下来,作为c2的b1
            $this->aAsC2B1($res['a'],$new_id);

            //更新以b1为首的单条记录
            $this->updateTopWithB1Team($new_id,$rec_place_res['b1'],$res['a']);
        }else{
            //未达成team
            //更新以b1为首的单条记录
            $this->updateTopWithB1NoTeam($new_id,$rec_place_res['b1']);
        }
    }

    //更新status
    public function updateStatus($team_id){
        $status['status'] = 1;
        Db::table('store_teams')->where('id = '.$team_id)->update($status);
    }

    //a下来,作为c2的b1
    public function aAsC2B1($a,$new_id){
        $data['b1'] = $a;
        Db::table('store_teams')->where('a = '.$new_id.' and status = 0')->update($data);
    }

    //更新c3处的值
    public function updateC3Value($new_id,$team_id){
        $data['c3'] = $new_id;
        Db::table('store_teams')->where('id = '.$team_id)->update($data);
    }

    //更新以b2为首的单条记录(未达成team版)
    public function updateTopWithB2NoTeam($new_id,$b2){
        $res = Db::table('store_teams')->field('b1,b2')->where('a = '.$b2.' and status = 0')->order('id desc')->find();
        if($res['b1'] == ''){
            $data['b1'] = $new_id;
        }else{
            $data['b2'] = $new_id;
        }
        Db::table('store_teams')->where('a = '.$b2.' and status = 0')->update($data);
    }

    //更新c4处的值
    public function updateC4Value($new_id,$team_id){
        $data['c4'] = $new_id;
        Db::table('store_teams')->where('id = '.$team_id)->update($data);
    }

    //a下来,作为c4的b1
    public function aAsC4B1($a,$new_id){
        $data['b1'] = $a;
        Db::table('store_teams')->where('a = '.$new_id.' and status = 0')->update($data);
    }

    //更新c4相关(无需判断是否达成team)
    public function checkC4About($team_id,$new_id,$rec_place_res){
        $res = Db::table('store_teams')->field('a,b1,c1,c2')->where('id = '.$team_id)->find();
        //更新status
        $this->updateStatus($team_id);

        //a下来,作为c2的b1
        $this->aAsC4B1($res['a'],$new_id);

        //更新以b2为首的单条记录
        $this->updateTopWithB2Team($new_id,$rec_place_res['b1'],$res['a']);

    }

    //更新以b2为首的单条记录(达成team版)
    public function updateTopWithB2Team($new_id,$b1,$a){
        $data['b2'] = $new_id;
        $data['c3'] = $a;
        Db::table('store_teams')->where('a = '.$b1.' and status = 0')->update($data);

        //更新推荐人的推荐人直销收入字段
        $res = Db::table('store_members')->field('rec_id')->where('id = '.$a)->find();
        $res1 = Db::table('store_members')->field('ketixian,direct_selling_income')->where('id = '.$res['rec_id'])->find();
        $mem_data['direct_selling_income'] = number_format($res1['direct_selling_income'] + 9.99,2,".","");
        $mem_data['ketixian'] = number_format($res1['ketixian'] + 9.99,2,".","");
        Db::table('store_members')->where('id = '.$res['rec_id'])->update($mem_data);

        //生成一条以$a为首的记录
        $new_data = [
            'a'  => $a,
            'rec_id' => $res['rec_id'],
            'create_time'  => date('Y-m-d H:i:s')
        ];
        Db::table('store_teams')->insert($new_data);
    }

    //更新c4相关(需判断是否达成team)
    public function checkC4About1($team_id,$new_id,$rec_place_res){
        $res = Db::table('store_teams')->field('a,b1,c1,c2')->where('id = '.$team_id)->find();
        if($res['b1'] != '' && $res['c1'] != '' && $res['c2'] != ''){
            //达成team
            //更新status
            $this->updateStatus($team_id);

            //a下来,作为c4的b1
            $this->aAsC4B1($res['a'],$new_id);

            //更新以b2为首的单条记录
            $this->updateTopWithB2Team($new_id,$rec_place_res['b2'],$res['a']);
        }else{
            //未达成team
            //更新以b2为首的单条记录
            $this->updateTopWithB2NoTeam($new_id,$rec_place_res['b2']);
        }
    }

    //以指定用户为首的组织信息
    public function detail1(){

        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;

        $res = Db::table('store_teams')->alias('t')->field('t.id as t_id,t.a,t.b1,t.b2,t.c1,t.c2,t.c3,t.c4,t.rec_id,mem1.nickname as a_name,mem2.nickname as b1_name,mem3.nickname as b2_name,mem4.nickname as c1_name,mem5.nickname as c2_name,mem6.nickname as c3_name,mem7.nickname as c4_name,mem8.nickname as rec_name')
            ->join('store_members mem1','mem1.id = t.a','LEFT')
            ->join('store_members mem2','mem2.id = t.b1','LEFT')
            ->join('store_members mem3','mem3.id = t.b2','LEFT')
            ->join('store_members mem4','mem4.id = t.c1','LEFT')
            ->join('store_members mem5','mem5.id = t.c2','LEFT')
            ->join('store_members mem6','mem6.id = t.c3','LEFT')
            ->join('store_members mem7','mem7.id = t.c4','LEFT')
            ->join('store_members mem8','mem8.id = t.rec_id','LEFT')
            ->where('t.status = 1')
            ->order('t.create_time desc')
            ->limit($offset,$limit)
            ->select();

        $count = Db::table('store_teams')->alias('t')
            ->where('t.status = 1')
            ->count();

        //总页码
        $pages = ceil($count / $limit);

        return $this->fetch('members/detail1',[
            'res'  => $res,
            'current_page'  => $current_page,
            'pages'  => $pages

        ]);
    }

    //以指定用户为首的组织信息
    public function detail2(){

        $current_page = input('get.current_page',1,'intval');
        $limit = 10;
        $offset = ($current_page - 1) * $limit;

        $res = Db::table('store_teams')->alias('t')->field('t.id as t_id,t.a,t.b1,t.b2,t.c1,t.c2,t.c3,t.c4,t.rec_id,mem1.nickname as a_name,mem2.nickname as b1_name,mem3.nickname as b2_name,mem4.nickname as c1_name,mem5.nickname as c2_name,mem6.nickname as c3_name,mem7.nickname as c4_name,mem8.nickname as rec_name')
            ->join('store_members mem1','mem1.id = t.a','LEFT')
            ->join('store_members mem2','mem2.id = t.b1','LEFT')
            ->join('store_members mem3','mem3.id = t.b2','LEFT')
            ->join('store_members mem4','mem4.id = t.c1','LEFT')
            ->join('store_members mem5','mem5.id = t.c2','LEFT')
            ->join('store_members mem6','mem6.id = t.c3','LEFT')
            ->join('store_members mem7','mem7.id = t.c4','LEFT')
            ->join('store_members mem8','mem8.id = t.rec_id','LEFT')
            ->where('t.status = 0')
            ->order('t.create_time desc')
            ->limit($offset,$limit)
            ->select();

        $count = Db::table('store_teams')->alias('t')
            ->where('t.status = 0')
            ->count();

        //总页码
        $pages = ceil($count / $limit);

        return $this->fetch('members/detail2',[
            'res'  => $res,
            'current_page'  => $current_page,
            'pages'  => $pages

        ]);
    }

    //获取组织详细信息
    public function getDetail(){
        $id = input('get.id');

        $res = Db::table('store_teams')->alias('t')->field('t.id as t_id,t.a,mem1.nickname as a_name,t.b1,mem2.nickname as b1_name,t.b2,mem3.nickname as b2_name,t.c1,mem4.nickname as c1_name,t.c2,mem5.nickname as c2_name,t.c3,mem6.nickname as c3_name,t.c4,mem7.nickname as c4_name,mem8.nickname as rec_name')
            ->join('store_members mem1','mem1.id = t.a','LEFT')
            ->join('store_members mem2','mem2.id = t.b1','LEFT')
            ->join('store_members mem3','mem3.id = t.b2','LEFT')
            ->join('store_members mem4','mem4.id = t.c1','LEFT')
            ->join('store_members mem5','mem5.id = t.c2','LEFT')
            ->join('store_members mem6','mem6.id = t.c3','LEFT')
            ->join('store_members mem7','mem7.id = t.c4','LEFT')
            ->join('store_members mem8','mem8.id = t.rec_id','LEFT')
            ->where('t.id = '.$id)
            ->find();

        return $this->fetch('members/getDetail',[
            'res'  => $res
        ]);
    }

}
