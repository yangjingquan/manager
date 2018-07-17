<?php
namespace app\common\model;
use think\Model;
use think\Db;
class BisAccount extends Model{

    //添加账户信息
    public function add($param){
        $data = [
            'bis_id'  => session('bis_id','','bis'),
            'bank_name'   => $param['bank_name'],
            'account'   => $param['account'],
            'account_name'   => $param['account_name'],
            'kaihuhang'   => $param['kaihuhang'],
            'mobile'   => $param['mobile'],
        ];
        $res = Db::table('store_bis_account')->insert($data);
        return $res;
    }

    //修改账户信息
    public function updateAccount($param){
        $data = [
            'bank_name'   => $param['bank_name'],
            'account'   => $param['account'],
            'account_name'   => $param['account_name'],
            'kaihuhang'   => $param['kaihuhang'],
            'mobile'   => $param['mobile'],
        ];

        $res = Db::table('store_bis_account')->where('id = '.$param['id'])->update($data);
        return $res;
    }

    //提现操作
    public function apply_tixian($id, $available_money, $tixian_account, $tixian_money){
        $data = [
            'available_money' => $available_money - $tixian_account,
            'tixian_money' => $tixian_money + $tixian_account,
        ];
        $res = Db::table('store_bis_account')->where('id = '.$id)->update($data);

        if($res) {
            $records_data = [
                'bis_id' => session('bis_id', '', 'bis'),
                'amount' => $tixian_account,
                'tixian_status' => 0,
                'create_time' => date('Y-m-d H:i:s')
            ];

            $records_res = Db::table('store_tixian_records')->insert($records_data);
            if($records_res){
                return true;
            }
        }else{
            return false;
        }
    }

    //财务明细页面
    public function getFinanceRecords($bis_id,$type=0,$limit,$offset){
        if($type != 0){
            $where = ' and fr.type ='.$type;
        }else{
            $where = '';
        }
        $res = Db::table('store_finance_records')->alias('fr')->field('fr.id as fr_id,fr.type,fr.amount,fr.operator,fr.ip,fr.create_time,fr.remarks,bis_users.username')
                ->join('store_bis_admin_users bis_users','fr.operator_id = bis_users.id','LEFT')
                ->where('fr.bis_id = '.$bis_id.' and fr.status = 1'.$where)
                ->order('fr.create_time desc')
                ->limit($offset,$limit)
                ->select();
        return $res;
    }

    //财务明细总数
    public function getFinanceRecordsCount($bis_id,$type=0){
        if($type != 0){
            $where = ' and fr.type ='.$type;
        }else{
            $where = '';
        }
        $res = Db::table('store_finance_records')->alias('fr')
            ->join('store_bis_admin_users bis_users','fr.operator_id = bis_users.id','LEFT')
            ->where('fr.bis_id = '.$bis_id.' and fr.status = 1'.$where)
            ->count();

        return $res;
    }


}

?>