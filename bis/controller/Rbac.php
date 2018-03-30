<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/20
 * Time: 14:48
 */

namespace app\bis\controller;


use think\Db;

class Rbac extends Base
{
//角色与 人员角色权限关系表 为同一张表，原因是字段少并且习惯了
    public function role(){
        $bis_id=session('bisaccount')['bis_id'];
        $name=Db::table('o2o_bis_account')->where('bis_id',$bis_id)->select()->toArray();
        $data=Db::table('o2o_bis_role')->select()->toArray();
  //      halt($name);

        $role_ids=[];
        foreach ($data as $d=>$vo){
            if(in_array($vo['role_id'],$role_ids)){
     //           $auth_ids[$vo['role_id']][]=array($vo['auth_id']);
                unset($data[$d]);
            }else{
                $role_ids[]=$vo['role_id'];
//                $auth_ids[$vo['role_id']][]=array($vo['auth_id']);
            }
        }

        foreach ($name as $id=>$val){
            foreach ($data as $d=>$vo){
                if($val['role_id']==$vo['role_id']){
                    $data[$d]['username'][]=$val['username'];
                }
            }
        }

        foreach ($data as $key=>$val){
            if(array_key_exists('username',$val)){
                $data[$key]['username']=implode(',',$val['username']);
            }else{
                $data[$key]['username']='';
            }
        }
      //  halt($data);

        return $this->fetch('',[
            'data'=>$data,
        ]);
    }
    public function roleadd(){
        $data=Db::table('o2o_bis_auth')->select()->toArray();
        $res=[];
        foreach ($data as $val){
            $res[$val['controller_name']][]=['name'=>$val['action_name'],'id'=>$val['id']];
        }

//        halt($res);

        return $this->fetch('',[
           'res'=>$res,
        ]);
    }

    public function save(){
        $data=input('post.');
        if(array_key_exists('role_id',$data)){
            $role_id=$data['role_id'];
            Db::table('o2o_bis_role')->where('role_id',$data['role_id'])->delete();

        }else{
            $role_id=Db::table('o2o_bis_role')->max('role_id');
            $role_id=++$role_id;
        }

        $auth_id=array_key_exists('auth_id',$data)?$data['auth_id']:[999];

        foreach ($auth_id as $vo){
            $res[]=array(
                'role_id'=>$role_id,
                'name'=>$data['name'],
                'comment'=>$data['comment'],
                'auth_id'=>$vo,
                );
        }

        $result=Db::table('o2o_bis_role')->insertAll($res);
        if($result){
            return $this->result('',1,'');
        }else{
            return $this->result('',0,'数据库存入出错');
        }
    }

    public function roledel($id){
        $res=Db::table('o2o_bis_role')->where('role_id',$id)->delete();
        return $this->result('',1,'');

    }
    public function roledit($role_id){
        $auth_ids=Db::table('o2o_bis_role')->where('role_id',$role_id)->column('auth_id');
        $name=Db::table('o2o_bis_role')->where('role_id',$role_id)->value('name');
        $data=Db::table('o2o_bis_auth')->select()->toArray();

        $res=[];
        foreach ($data as $val){
            $res[$val['controller_name']][]=[
                'name'=>$val['action_name'],
                'id'=>$val['id'],

            ];
        }



        return $this->fetch('',[
            'res'=>$res,
            'auth_ids'=>$auth_ids,
            'role_id'=>$role_id,
            'name'=>$name,
        ]);
    }



    public function account(){
        $param=input('get.');
        $data=model('bisAccount')->getAll(session('bisaccount')['bis_id']);
        $count=count($data);
        $this->assign('count',$count);
        $this->assign('data',$data);
        $view='';

        if(key_exists('act',$param) && $param['act'] == 'add'){
            $view=$this->accountAdd();
        }
        if(key_exists('id',$param) && key_exists('act',$param) && $param['act'] == 'edit' ){
            $id=$param['id'];
            $view=$this->accountEdit($id);
        }

        return $this->fetch($view,[

        ]);

    }
    public function accountAdd(){
        $roles=Db::table('o2o_bis_role')->select()->toArray();
        $roles=repeat($roles,'role_id');
        $this->assign('roles',$roles);

        return $view='accountAdd';

    }
    public function accountSave(){
        $data=input('post.');
        if(array_key_exists('id',$data)){
           $res=Db::table('o2o_bis_account')->where('id',$data['id'])->update($data);
        }else{
            $res=model('BisAccount')->addacc($data);
        }

        if($res){
            return $this->result('',1,'');
        }else{
            return $this->result('',0,'数据保存出错');
        }

    }
    public function accountEdit($id){
        $roles=Db::table('o2o_bis_role')->select()->toArray();
        $roles=repeat($roles,'role_id');
        $this->assign('roles',$roles);
        $data=model('BisAccount')->find($id);
        $this->assign('data',$data);

        return $view='accountEdit';
    }

}