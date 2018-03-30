<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/12
 * Time: 8:53
 */

namespace app\bis\controller;


use think\Controller;
use think\Db;

class Deal extends Base
{
    public function index(){

        $bisid=session('bisaccount')['bis_id'];
        $data=model('Deal')->getAll($bisid);
 //       halt($data);
        return $this->fetch('',[
            'data'=>$data,
        ]);
    }



    public function add(){
        $data=session('bisaccount');
        $main_location=$data['main_location'];
        $main_location['se_category_id']=json_decode($main_location['se_category_id']);

        $bisid=session('bisaccount')['bis_id'];
        $locations=model('bisLocation')->getByBisId($bisid);
        $citys=Db::table('o2o_city')->where('status',1)->select();
        $categorys=Db::table('o2o_category')->where('status',1)->select();

        foreach ($categorys as $id=>$vo){
            if($vo['parent_id']==0){
                $categoryPdata[]=$vo;
            }else{
                $categorySe[$vo['id']]=$vo;
            }
        }

        foreach ($citys as $id=>$vo){
            if($vo['parent_id']==0){
                $cityPdata[]=$vo;
            }else{
                $citySe[$vo['id']]=$vo;
            }
        }


        return $this->fetch('',[
            'citys'=>$cityPdata,
            'categorys'=>$categoryPdata,
            'se_city'=>$citySe,
            'se_category'=>$categorySe,
            'main'=>$main_location,
            'locations'=>$locations,
        ]);
    }

    public function save(){
        $data=input('post.');


        if(array_key_exists('se_category_id',$data)){

            $data['se_category_id']=implode(',',$data['se_category_id']);

        }
        if(array_key_exists('locations',$data)){
            $data['location_ids']=implode(',',$data['locations']);
            unset($data['locations']);
        }


        $data['start_time']=strtotime($data['start_time']);
        $data['end_time']=strtotime($data['end_time']);
        $data['coupons_begin_time']=strtotime($data['coupons_begin_time']);
        $data['coupons_end_time']=strtotime($data['coupons_end_time']);


        if(array_key_exists('id',$data)){
            $res=Db::table('o2o_deal')->update($data,$data['id']);
        }else{
            $data['bis_account_id']=session('bisaccount')['id'];
            $data['bis_id']=session('bisaccount')['bis_id'];
            $data['status']=2;
            $res=model('deal')->add($data);
        }

        if($res){
            return $this->result('',1,'');
        }else{
            return $this->result('',0,'数据存入失败');
        }


    }
    public function see($id){
        $data=model('deal')->get($id);
        // halt($data);
        $city_name=Db::table('o2o_city')->where('id',$data['city_id'])->value('name');
        $bisid=session('bisaccount')['bis_id'];
        $locations=model('bisLocation')->getByBisId($bisid);
        $cityPdata=Db::table('o2o_city')->where('parent_id',0)->where('status',1)->select();
        $categoryPdata=Db::table('o2o_category')->where('parent_id',0)->where('status',1)->select();

        $se_category=Db::table('o2o_category')->where('parent_id',$data['category_id'])->select();

        $data['location_ids']=explode(',',$data['location_ids']);
        $data['se_category_id']=explode(',',$data['se_category_id']);
        //  halt($data['location_ids']);

        return $this->fetch('',[
            'citys'=>$cityPdata,
            'categorys'=>$categoryPdata,
            'locations'=>$locations,
            'data'=>$data,
            'city_name'=>$city_name,
            'se_category'=>$se_category,
        ]);
    }

    public function edit($id){
        $data=model('deal')->get($id)->toArray();

        $city_name=Db::table('o2o_city')->where('id',$data['city_id'])->value('name');

        $bisid=session('bisaccount')['bis_id'];
        $locations=model('bisLocation')->getByBisId($bisid);
        $cityPdata=Db::table('o2o_city')->where('parent_id',0)->where('status',1)->select();
        $categoryPdata=Db::table('o2o_category')->where('parent_id',0)->where('status',1)->select();

        $se_category=Db::table('o2o_category')->where('parent_id',$data['category_id'])->select();

            $data['location_ids']=explode(',',$data['location_ids']);
            $data['se_category_id']=explode(',',$data['se_category_id']);
          //  halt($data['location_ids']);

        return $this->fetch('',[
            'citys'=>$cityPdata,
            'categorys'=>$categoryPdata,
            'locations'=>$locations,
            'data'=>$data,
            'city_name'=>$city_name,
            'se_category'=>$se_category,
        ]);
    }
    public function status(){
        $res['code']=1;
        $data=input('post.');
        $status=$data['status'];
        $id=$data['id'];


        if(!is_numeric($status) || !is_numeric($id) ){
            $res['msg']='参数出错，请检查js代码';
            $res['code']=0;
            return $res;
        }

        $data=model('Deal')->save(['status'=>$status],['id'=>$id]);

        if($data!==1){
            $res['code']=0;
            $res['msg']='后端业务故障，数据未成功保存';
            return $res;
        }

        return $res;
    }

    public function del(){
        $res['code']=1;
        $data=input('post.');
        $status=$data['status'];
        $id=$data['id'];


        if(!is_numeric($status) || !is_numeric($id) ){
            $res['msg']='参数出错，请检查js代码';
            $res['code']=0;
            return $res;
        }

        $data=model('Deal')->save(['status'=>$status],['id'=>$id]);

        if($data!==1){
            $res['code']=0;
            $res['msg']='后端业务故障，数据未成功保存';
            return $res;
        }

        return $res;
    }

    public function checked(){
        $bisid=session('bisaccount')['bis_id'];
        $data=model('Deal')->getChecked($bisid);
        $count=$data->count();


        return $this->fetch('',[
            'data'=>$data,
            'count'=>$count
        ]);
    }
    public function checkedsee($id){
        $data=model('deal')->get($id);
        // halt($data);
        $city_name=Db::table('o2o_city')->value('name',$data['city_id']);
        $bisid=session('bisaccount')['bis_id'];
        $locations=model('bisLocation')->getByBisId($bisid);
        $cityPdata=Db::table('o2o_city')->where('parent_id',0)->where('status',1)->select();
        $categoryPdata=Db::table('o2o_category')->where('parent_id',0)->where('status',1)->select();

        $se_category=Db::table('o2o_category')->where('parent_id',$data['category_id'])->select();

        $data['location_ids']=explode(',',$data['location_ids']);
        $data['se_category_id']=explode(',',$data['se_category_id']);
        //  halt($data['location_ids']);

        return $this->fetch('',[
            'citys'=>$cityPdata,
            'categorys'=>$categoryPdata,
            'locations'=>$locations,
            'data'=>$data,
            'city_name'=>$city_name,
            'se_category'=>$se_category,
        ]);
    }






































    public function test(){
        $ak=config('map.ak');
        return $this->fetch('',[
            'ak'=>$ak,
        ]);
    }
}