<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/16
 * Time: 14:30
 */

function locationstatus($status){
    if($status == 0){
        $res='<a style="text-decoration:none" class="ml-5" title="点击启用"><span onclick="o2o_status(this,1)" class="label label-warning radius">停  用</span></a>';
    }elseif ($status == 1){
        $res='<a style="text-decoration:none" class="ml-5" title="点击停用"><span onclick="o2o_status(this,0)" class="label label-success radius">正  常</span></a>';
    }elseif ($status == 2){
        $res='<a><span class="label label-secondary radius">待 审 核</span></a>';
    }elseif ($status == -1){
        $res='<a><span class="label label-danger radius">删  除</span></a>';
    }
    return $res;
}
function dealstatus($status){
    if($status == 0){
        $res='<a style="text-decoration:none" class="ml-5" title="点击上架"><span onclick="o2o_status(this,1)" class="label label-warning radius">已下架</span></a>';
    }elseif ($status == 1){
        $res='<a style="text-decoration:none" class="ml-5" title="点击下架"><span onclick="o2o_status(this,0)" class="label label-success radius">上架中</span></a>';
    }elseif ($status == 2){
        $res='<span class="label label-secondary radius">待 审 核</span>';
    }elseif ($status == -1){
        $res='<span class="label label-danger radius">删  除</span>';
    }
    return $res;
}

function accountstatus($status){
    if($status == -1){
        $res='<span class="label label-warning radius">已停用</span>';
    }elseif ($status == 1){
        $res='<span  class="label label-success radius">启用中</span>';
    }elseif ($status == 2){
        $res='<span class="label label-secondary radius">待 审 核</span>';
    }elseif ($status == 0){
        $res='<span class="label label-danger radius">删  除</span>';
    }
    return $res;
}

/*
 * 删除二维数组中$base重复的值
 */
function repeat($data,$base){
    $array=array();
    foreach ($data as $id=>$vo){
        if(in_array($vo[$base],$array)){
            unset($data[$id]);
        }else{
            $array[]=$vo[$base];
        }
    }
    return $data;
}