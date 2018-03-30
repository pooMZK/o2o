<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/2
 * Time: 16:02
 */

namespace app\admin\model;


use think\Model;

class Category extends Model
{
    protected $hidden=['update_time',];

    public function getStatusAttr($value)
    {
        $status = [-1=>'<a><span class="label label-danger radius">删  除</span></a>',
            0=>'<a><span class="label label-warning radius">禁  用</span></a>',
            1=>'<a><span class="label label-success radius">正  常</span></a>',
            2=>'<a><span class="label label-secondary radius">待 审 核</span></a>'];
        return $status[$value];
    }
    public function getParentData($id=0){
        $where=[
            'parent_id'=>$id,
            'status'=>['>',-1]
        ];

        $data=$this->where($where)
            ->select();

        return $data;

    }


}