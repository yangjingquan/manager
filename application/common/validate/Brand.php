<?php
namespace app\common\validate;
use think\Validate;
class Brand extends Validate{
    protected $rule = [
        ['brand_name','require|max:30', '品牌名必须传递|品牌名不能超过30个字符'],
        ['id','number','id必须是数字'],
        ['status','number|in:-1,0,1','状态必须是数字|状态范围不合法'],

    ];


    //场景设置
    protected $scene = [
        'add'  =>  ['brand_name'],//添加
        'status'  => ['id','status'],//修改状态
        'update'   => ['id','brand_name'],//修改
    ];

}
?>