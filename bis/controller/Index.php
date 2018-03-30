<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/12
 * Time: 8:54
 */

namespace app\bis\controller;




use think\Db;
use think\Exception;

class Index extends Base
{
    public function index(){
        $session=session('bisaccount');
        $name=$session['name'];
        $bis_name=Db::table('o2o_bis')->where('id',$session['bis_id'])->value('name');

        return $this->fetch('index',[
           'name'=>$name,
            'bis_name'=>$bis_name,
        ]);

    }
    public function welcome(){
        return $this->fetch();
    }

}