<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/13
 * Time: 17:58
 */

namespace app\bis\model;


use think\Model;

class BisLocation extends Model
{
    /*
     * 添加总店
     */
    public function addmain($data){
        $address=$data['address'];
        $lnglat=\Map::getLngLat($address);
        $data['lnglat']=$lnglat;
        $data['is_main']=1;
        $res=$this->data($data)
            ->allowField(true)
            ->save();
        return $res;
    }

    /*
     * 添加分店
     */
    public function add($data){
        $address=$data['address'];
        $lnglat=\Map::getLngLat($address);
        $data['lnglat']=$lnglat;

        $res=$this->data($data)
            ->allowField(true)
            ->save();
        return $res;
    }

    /*
     * 修改保存
     */
    public function edit($data){
        $address=$data['address'];
        $lnglat=\Map::getLngLat($address);
        $data['lnglat']=$lnglat;

        $data['se_category_id']=array_key_exists('se_category_id',$data)?json_encode($data['se_category_id']):'';


        $res=$this->allowField(true)->save($data,['id'=>$data['id']]);

        return $res;
    }

    /*
     * 状态获取器
     */
//    public function getStatusAttr($value)
//    {
//        $status = [-1=>'<a><span class="label label-danger radius">删  除</span></a>',
//            0=>'<a><span class="label label-warning radius">禁  用</span></a>',
//            1=>'<a><span class="label label-success radius">正  常</span></a>',
//            2=>'<a><span class="label label-secondary radius">待 审 核</span></a>'];
//        return $status[$value];
//    }
    /*
     * 是否总店获取器
     */
    public function getIsMainAttr($value)
    {
        $status = [0=>'分店',1=>'总店'];
        return $status[$value];
    }

    /*
     * 商户的唯一标示是bis_id
     */
    public function getByBisId($bisid){

        $res=$this->where([
            'status'=>['>',-1],
            'bis_id'=>$bisid,
        ])
            ->order('is_main','desc')
            ->select();
        return $res;

    }

}