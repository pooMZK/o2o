<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/12
 * Time: 8:54
 */

namespace app\bis\controller;


use app\bis\model\BisAccount;
use app\bis\severice\Bisredisdata;
use think\Controller;
use think\Db;
use think\Session;
use think\Validate;

class Login extends Controller
{
    public function index(){
        if(session('?bisaccount')){
            $this->redirect('bis/index/index');
        }
        return $this->fetch('',[]);
    }

    /*
     * 登录验证层，不能用validate做自定义方法，重名，所以加了P
     */
    public function validateP(){
        $data=input('post.');
        //规则验证开始
        $validate=new Validate([
            'name|用户名'=>'require|max:25|token',//token表单验证，其实前端也写了提交按钮点击消失的代码
            'password|密码'=>'require',
        ]);
        if(!$validate->check($data)){
        //    dump($validate->getError());
            $this->error($validate->getError());
        }
        //规则验证结束

        //数据验证开始：其他模块写过ajax验证密码与用户名，考虑到展示的目的，并且就两个字段这里用纯后台方式验证，依然是不访问mysql
        $redis=redis();
        Bisredisdata::bisAccount();//运行环境时根据状况修改redis数据刷新位置

        if($redis->HEXISTS('用户名数据',$data['name'])){
            //刷新此redis哈希表只需要调一下service层的静态方法行为就行了，此处不宜刷新redis数据
           $pdata=$redis->hGet('用户名数据',$data['name']);
           $pdata=json_decode($pdata,true);//取出数组化
           $password=md5($data['password'].config('secure.o2o_salt').$pdata['code']);
              if($password !== $pdata['password']){
                 $this->error('密码错误');
                 }

           $data['bis_id']=$pdata['bis_id'];//取出bis_id存入session，
           $data['id']=$pdata['id'];//登录用户名id
           $data['role_id']=$pdata['role_id'];//登录用户名id
        }else{
            $this->error('用户名不存在');
        }

        //数据验证结束
        // 测试是成功的，其他的验证逻辑就不写那么多解释了
        // 另外：考虑到某些东西，有三个应该是唯一的字段数据库是没有限定的：email，tel，username，只在逻辑层做处理，本系统严谨的唯一字段是bis_id
        $main_location=Db::table('o2o_bis_location')->where('is_main',1)->where('bis_id',$pdata['bis_id'])->find();
        $data['main_location']=$main_location;
        session('bisaccount',$data);//存入session
        //session数据示例：{ "bisaccount": { "__token__": "f60ebfeec6ce61f89ab617bc9adc4c29", "name": "admin", "password": "admin", "bis_id": 3 } }



        $this->redirect('/bis/index/index');
    }

    public function updateP(){
        $data=Db::table('o2o_bis_account')->where('username','admin')->find();
        $code=$data['code'];
        $password=md5('admin'.config('secure.o2o_salt').$code);
        Db::table('o2o_bis_account')->where('id',$data['id'])->setField('password',$password);
        Bisredisdata::bisAccount();
    }

}