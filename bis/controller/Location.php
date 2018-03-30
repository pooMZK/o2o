<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/12
 * Time: 8:54
 */

namespace app\bis\controller;


use think\Controller;
use think\Db;

class Location extends Base
{
    public function index(){
        $bisid=session('bisaccount')['bis_id'];
        $data=model('bisLocation')->getByBisId($bisid);

        //$data=Db::table('o2o_bis_location')->where('status','>',-1)->where('bis_id',$id)->select();
        return $this->fetch('',[
            'data'=>$data,
        ]);
    }


    public function add(){
        $data=session('bisaccount');
        $main_location=$data['main_location'];
        $main_location['se_category_id']=json_decode($main_location['se_category_id']);

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
        ]);
    }


    /*
     * 添加与修改的后续保存操作
     */
    public function save(){
        $data=input('post.');

        $data['is_main'] = 0;
        $data['bis_id'] = session('bisaccount')['bis_id'];
        if(array_key_exists('id',$data)){
            $res = model('BisLocation')->edit($data);

            if ($res) {
                return $this->result('', 1, '修改成功');
            } else {
                return $this->result('', 0, '数据库修改出错');
            }
        }else {

            $res = model('BisLocation')->add($data);
            if ($res) {
                return $this->result('', 1, '添加成功');
            } else {
                return $this->result('', 0, '数据库添加出错');
            }
        }


    }

    /*
     * 不说了，写了一个多钟头：数据库数据混乱
     */
    public function edit($id){
        $cityPdata=Db::table('o2o_city')->where('parent_id',0)->select();
        $categoryPdata=Db::table('o2o_category')->where('parent_id',0)->select();
        $data=model('BisLocation')->get($id);
        $se_city_id=$data['se_city_id'];
        $se_city_name=Db::table('o2o_city')->where('id',$se_city_id)->value('name');
        $se_cat_id=json_decode($data['se_category_id']);

        $se_cat_name=[];
        if(is_array($se_cat_id)){
           foreach ($se_cat_id as $val){
            $se_cat_name[]=Db::table('o2o_category')->where('id',$val)->find();

          }
        }else{

            $se_cat_name=Db::table('o2o_category')->where('id',$se_cat_id)->find();

        }


        return $this->fetch('',[
           'data'=>$data,
            'citys'=>$cityPdata,
            'categorys'=>$categoryPdata,
            'se_city_name'=>$se_city_name,
            'se_cat_name'=>$se_cat_name
        ]);
    }

    /*
     * 删除"本地"mysql重复"name"的数据（以前测试遗留）
     */
    public function datadel(){
        $data=Db::table('o2o_bis')->column('name','id');
        foreach ($data as $id=>$val){
            $base=$val;unset($data[$id]);
            foreach ($data as $id1=>$val1){
                if($base == $val1){
                    Db::table('o2o_bis')->where('id',$id1)->update(['status'=>3]);
                }
            }
        }
        Db::table('o2o_bis')->where('status',3)->delete();

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

        $data=Db::table('o2o_bis_location')->update(['status'=>$status,'id'=>$id]);

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

        $data=model('BisLocation')->save(['status'=>$status],['id'=>$id]);

        if($data!==1){
            $res['code']=0;
            $res['msg']='后端业务故障，数据未成功保存';
            return $res;
        }

        return $res;
    }

    public function see($id){
        $cityPdata=Db::table('o2o_city')->where('parent_id',0)->select();
        $categoryPdata=Db::table('o2o_category')->where('parent_id',0)->select();
        $data=model('BisLocation')->get($id);
        $se_city_id=$data['se_city_id'];
        $se_city_name=Db::table('o2o_city')->where('id',$se_city_id)->value('name');
        $se_cat_id=json_decode($data['se_category_id']);

        $se_cat_name=[];
        if(is_array($se_cat_id)){
            foreach ($se_cat_id as $val){
                $se_cat_name[]=Db::table('o2o_category')->where('id',$val)->find();

            }
        }else{

            $se_cat_name=Db::table('o2o_category')->where('id',$se_cat_id)->find();

        }


        return $this->fetch('',[
            'data'=>$data,
            'citys'=>$cityPdata,
            'categorys'=>$categoryPdata,
            'se_city_name'=>$se_city_name,
            'se_cat_name'=>$se_cat_name
        ]);
    }

}