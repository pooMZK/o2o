<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/2/28
 * Time: 13:49
 */

namespace app\admin\controller;


use think\Controller;

class Index extends Controller
{
    public function index(){
        return $this->fetch();
    }

    public function test(){
        return $this->fetch();
    }



}