<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/9
 * Time: 18:50
 */

namespace app\admin\model;


use think\Model;

class City extends Model
{
    protected $hidden=['update_time','is_default','uname'];

    public function getStatusAttr($value)
    {
        $status = [-1=>'<a><span class="label label-danger radius">删  除</span></a>',
            0=>'<a><span class="label label-warning radius">禁  用</span></a>',
            1=>'<a><span class="label label-success radius">正  常</span></a>',
            2=>'<a><span class="label label-secondary radius">待 审 核</span></a>'];
        return $status[$value];
    }
    public function getByParentId($pid){
        $data=$this->where([
            'status'=>['>',-1],
            'parent_id'=>$pid,
        ])
            ->select();
        return $data->toArray();

    }

}