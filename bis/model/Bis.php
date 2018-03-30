<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/13
 * Time: 15:11
 */

namespace app\bis\model;


use think\Db;
use think\Model;

class Bis extends Model
{
    public function add($data){
        $newdata=array(
            'name'=>$data['name'],
            'email'=>$data['email'],
            'logo'=>$data['logo'],
            'licence_logo'=>$data['licence_logo'],
            'description'=>array_key_exists('description',$data)?$data['description']:'',
            'city_id'=>$data['city_id'],
            'se_city_id'=>$data['se_city_id']?$data['se_city_id']:'',
            'bank_info'=>$data['bank_info'],
            //'money'=>$data['money'],
            'bank_name'=>$data['bank_name'],
            'bank_user'=>$data['bank_user'],
            'faren'=>$data['faren'],
            //'status'=>0
            'status'=>1//测试环境
        );
       $res= Db::table('o2o_bis')->insertGetId($newdata);
//        $res=$this->data($data)
//            ->allowField(true);
//           $res=$res->save();
//           // ->getLastInsID();
      //  halt($res);
        return $res;

    }


    public function getStatusForWaiting($id){
        $status=self::where('id',$id)->value('status');
        switch ($status){
            case 0:
                $res='<h1>您的店铺申请正在审核中</h1>';
                break;
            case 1:
                $res='<h1>您的店铺申请已经通过；<a class="btn btn-link" href="/bis/login/index">点击登录</a></h1>';
                break;
            case -2:
                $res='<h1>您的店铺申请被拒绝，原因是：</h1>';
                break;
            default:
                $res='<h1>您的店铺申请不存在</h1>';
        }
        return $res;

    }



}