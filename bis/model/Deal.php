<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/18
 * Time: 13:51
 */

namespace app\bis\model;


use think\Model;

class Deal extends Model
{
    public function getStartTimeAttr($value)
    {
        $res=date("Y-m-d H:i:s",$value);
        return $res;
    }
    public function getEndTimeAttr($value)
    {
        $res=date("Y-m-d H:i:s",$value);
        return $res;
    }
    public function getCouponsBeginTimeAttr($value)
    {
        $res=date("Y-m-d H:i:s",$value);
        return $res;
    }
    public function getCouponsEndTimeAttr($value)
    {
        $res=date("Y-m-d H:i:s",$value);
        return $res;
    }

    public function getAll($bisid){
        $res=$this
            ->where([
                'bis_id'=>$bisid,
                'status'=>['in',[1,0]]
            ])
            ->select();
        return $res;
    }

    public function getChecked($bisid){
        $res=$this
            ->where([
                'bis_id'=>$bisid,
                'status'=>2
            ])
            ->select();
        return $res;
    }

    public function add($data){
        $res=$this->data($data)
            ->allowField(true)
            ->save();
        return $res;

    }

}