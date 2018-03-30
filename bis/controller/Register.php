<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/12
 * Time: 8:54
 */

namespace app\bis\controller;


use aliyun\Sms;
use phpmailer\Email;
use think\Controller;
use think\Db;

class Register extends Controller
{
    public function index(){
        $cityPdata=Db::table('o2o_city')->where('parent_id',0)->where('status',1)->select();
        $categoryPdata=Db::table('o2o_category')->where('parent_id',0)->where('status',1)->select();

        return $this->fetch('',[
            'cityp'=>$cityPdata,
            'categoryPdata'=>$categoryPdata
        ]);
    }
    public function save(){
        $data=input('param.');
        mylog($data);


        $data['se_city_id']=array_key_exists('se_city_id',$data)?$data['se_city_id']:'';
        $data['se_category_id']=array_key_exists('se_category_id',$data)?json_encode($data['se_category_id']):'';

        $res=model('bis')->add($data);
        $data['bis_id']=$res;
        $res1=model('BisLocation')->addmain($data);
        $res2=model('BisAccount')->addmain($data);
        $code=1;
        $msg=$data['bis_id'];
        if(!$res){
            $code=0;
            $msg.='数据存入总表出错';
        }
        if($res1!==1){
            $code=0;
            $msg.='数据存入门店表出错';
        }
        if($res2!==1){
            $code=0;
            $msg.='数据存入用户表出错';
        }
        Sms::send($data['tel'],'110');//发送短信//这只能发验证码，阿里云规定得再写个模板，审核要时间，逻辑如此而已
        Email::send($data['email'],'通知','<h1><a href="http://'.$_SERVER['HTTP_HOST'].'/bis/register/waiting?id='.$data['bis_id'].'">您的店铺正在审核中，点击查看审核状态</a></h1>');//发送邮件
        return $this->result('',$code,$msg);
    }

    public function waiting(){
       // $status=input('get.status');
        $id=input('get.id');
//        if(!$id || !is_int($id)){
//           return $this->redirect('/404.html');
//        }
        $msg=model('bis')->getStatusForWaiting($id);

        return $this->fetch('',[
            'msg'=>$msg,
        ]);
    }

}