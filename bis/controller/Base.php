<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/15
 * Time: 15:38
 */

namespace app\bis\controller;




use think\Controller;
use think\Db;

class Base extends Controller
{

    public function _initialize()
    {
        $res=session('?bisaccount');
        if($res !== true){
            $this->redirect('/bis/login/index');
        }else{
            $this ->RBAC();
        }

    }

    //权限控制方法
    public function RBAC(){
        //halt(session('bisaccount'));
        $role_id=session('bisaccount')['role_id'];
        $auth_ids=Db::table('o2o_bis_role')->where('role_id',$role_id)->column('auth_id');

        $auth=Db::table('o2o_bis_auth')->select()->toArray();
        foreach ($auth as $val){
            if(in_array($val['id'],$auth_ids)){
                $data[]=$val;
            }
        }

        $controller=request()->controller();
        $controller=strtolower($controller);
        $action=request()->action();
        $code=0;
        $data[]=array('controller'=>'index','action'=>'index');
        $data[]=array('controller'=>'index','action'=>'welcome');
        foreach ($data as $id=>$vo){

            if($controller == $vo['controller'] && $action==$vo['action']){
                $code=1;  break;
            }
        }

        if($code==0){
            return $this->redirect('/AUTH.html');
        }

    }

    public function logout(){
        session(null);
        echo "<script>history.back();</script>";
       // echo "<script>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
    }

}