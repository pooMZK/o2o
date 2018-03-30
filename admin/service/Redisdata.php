<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/9
 * Time: 15:27
 */

namespace app\admin\service;

use app\lib\enum\RedisDbIdEnum;

use think\Db;
class Redisdata
{
    //此处为手动更新reds数据的方法

    /**
     * 刷新关于分类的数据
     */
    public function categoryP(){
        $data=Db::table('o2o_category')->where('status','>','-1')->where('parent_id',0)->column('name','id');
        $data=array_unique($data);
        //  halt($data);
        $data2=Db::table('o2o_category')->where('status','>','-1')->column('name','parent_id');
        $data2=array_unique($data2);

        $redis=redis(RedisDbIdEnum::o2o);
        $redis->del('顶级分类id和name');
        $redis->hmSet('顶级分类id和name',$data);
        $redis->del('有子分类的项');
        $redis->hmSet('有子分类的项',$data2);
        //   $data=$redis->hGetAll('有子分类的项');
        //   halt($data);
    }

    public function cityP(){
        $data=Db::table('o2o_city')->where('status','>','-1')->where('parent_id',0)->column('name','id');
        $data=array_unique($data);
        //  halt($data);
        $data2=Db::table('o2o_city')->where('status','>','-1')->column('name','parent_id');
        $data2=array_unique($data2);

        $redis=redis(RedisDbIdEnum::o2o);
        $redis->del('省级id和name');
        $redis->hmSet('省级id和name',$data);
        $redis->del('有子城市的省');
        $redis->hmSet('有子城市的省',$data2);
        //   $data=$redis->hGetAll('有子分类的项');
        //   halt($data);
    }

    public function categoryAll(){


        $pid=redis()->HKEYS('顶级分类id和name');
        $pid[]=0;
      //  halt($pid);

        $data=model('category')->where('status','>',-1)->select()->toArray();

        $redis=redis();
        foreach ($pid as  $val){

            $arr=[];
            $category=[];
            foreach ($data as $value){
              //  var_dump($value['parent_id']);

                if($val==$value['parent_id']){
                    $arr[]=$value;

                }
                if(!empty($arr)){
                    $category[$val]=$arr;

                    $rdata=serialize($arr);
                    $redis->del('categorydata_'.$val);
                    $redis->set('categorydata_'.$val,$rdata);
                }

            }



        }

    }


}