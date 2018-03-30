<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/14
 * Time: 15:14
 */

namespace app\bis\severice;


use think\Db;

class Bisredisdata
{
    /*
     * 哈希表名：用户名数据  key：username（方便检测重名）；value:json字符串的整条数据
     * @param 状态为正常的用户信息
     * @return redis存入
     */
    public static function  bisAccount(){
        $data=Db::table('o2o_bis_account')->where('status',1)->select()->toArray();
        $redis=redis();
            foreach ($data as $val){
                $redis->hset('用户名数据',$val['username'],json_encode($val));
            }
        // $res=$redis->hGetAll('用户名数据');
      //  return $res;

    }

}