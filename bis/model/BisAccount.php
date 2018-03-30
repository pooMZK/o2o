<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/13
 * Time: 17:58
 */

namespace app\bis\model;


use think\Exception;
use think\Model;

class BisAccount extends Model
{
    public function addmain($data){
        $data['is_main']=1;
        $data['bis_id']=$data['bis_id'];
        $data['code']=rand(1000,9999);
        $data['status']=1;
        $data['role_id']=0;
        $data['password']=md5($data['password'].config('secure.o2o_salt').$data['code']);



        $res=$this->data($data)
            ->allowField(true)
            ->save();

        $redis=redis()->hSet('用户名数据',$data['username'],json_encode($data));//存入redis
        if($redis==false){
            throw new Exception('redis程序出错，哈希：用户名数据');
        }


        return $res;
    }

    public function addacc($data){
        $data['is_main']=0;
        $data['bis_id']=session('bisaccount')['bis_id'];
        $data['code']=rand(1000,9999);
        $data['status']=1;
        $data['password']=md5($data['password'].config('secure.o2o_salt').$data['code']);

        $res=$this->data($data)
            ->allowField(true)
            ->save();

        $redis=redis()->hSet('用户名数据',$data['username'],json_encode($data));//存入redis
        if($redis==false){
            throw new Exception('redis程序出错，哈希：用户名数据');
        }


        return $res;

    }



    public function role(){
        //两张表并不是一对一的关系，而我们只需要取出一个字段就可以了，所以这里不是乱用是利用
        return $this->belongsTo('BisRole','role_id','role_id');
    }

    public function getAll($bisid){
        $res=$this->where([
            'bis_id'=>$bisid,
            'status'=>['neq',0]
        ])
            ->with('role')
            ->select()

            ->toArray();
       // halt($res);
        return $res;

    }

}