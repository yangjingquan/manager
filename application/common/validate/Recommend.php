<?php
namespace app\common\validate;
use think\Validate;
class Recommend extends Validate{

    //定义规则
    protected $rule = [
        ['redirect_url','require', '跳转url必须填写'],
        ['type','require', '推荐位类型必须选择'],
        ['res_id','number', '推荐位id必须存在'],

    ];


    //场景设置
    protected $scene = [
        'add'  =>  ['redirect_url','type'],//添加
        'update'   => ['res_id','redirect_url','type'],//修改
    ];

}
?>