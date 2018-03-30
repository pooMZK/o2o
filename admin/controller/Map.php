<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/11
 * Time: 14:11
 */

namespace app\admin\controller;



use Aliyun\Sms;
use phpmailer\Email;
use think\Controller;

class Map extends Controller
{
    public function index(){
      //  \Map()::getLngLat("郑州市郑州大学地铁站");
       // $data=\Map::staticimage("113.532135,34.818039");
       // $data=\Map::getLngLat("河南省郑州市郑州大学新校区图书馆");
        $data=\Map::panorama("河南省郑州市郑州大学新校区图书馆");

        return "<img src='$data'>";
    }
    public function email(){
        //Email::send('1731512162@qq.com','eee','eee');
        $res=Sms::send('13526415290','33333');
        halt($res);
    }

}