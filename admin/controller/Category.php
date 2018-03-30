<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/2/28
 * Time: 14:08
 */

namespace app\admin\controller;


use app\admin\service\Redisdata;
use think\Controller;
use think\Db;
use think\Exception;


class Category extends Controller
{


    /**
     * 列表显示
     * @return mixed
     */
    public function index(){



        $id=input('get.id');
        $name=input('get.pname');
       // halt($id);null或者index传来的数据
        if($id&&$name){
           return $this->fetch('child',[//这里可以做静态化页面缓存，把缓存的静态化页面存入redis和mysql，这里做逻辑处理：是否有静态文件、session处理等，有的话直接冲定向
               'id'=>$id,
               'name'=>$name,
           ]);
        }

        return $this->fetch(//视图层用了基于jquery的插件datatables，也就不用写繁琐的ajax代码，只有table数据是需要交互的，甚至可以不用渲染，直接调静态页面
        );
    }

    /**
     * ajax数据层
     * @return json
     * 注意：未开启table插件所谓的服务器模式；那么也就是是说一次性传给客户端所需的所有数据，客户端进行计算
     * 插件建议一次性的数据不能超过10000条 分类模块暂时达不到那么大量级的数据
     */
    public function data(){
            // (new Redisdata())->categoryAll();exit;
        $id=input('get.id');
        if(!$id){
            $id=0;
        }
        $redis=redis();

//        if($redis->exists('categorydata_'.$id)){//如果Redis崩溃或被清空则刷新数据
//            (new Redisdata())->categoryAll();//刷新数据
//        }


        $data=$redis->get('categorydata_'.$id);
        $data=unserialize($data);


        $pid=redis()->HKEYS('顶级分类id和name');
        $phid=redis()->HKEYS('有子分类的项');


        $i=1;
        foreach ($data as $id =>$val){
               // $arr[$id][]=$data[$id]['id'];
                $arr[$id][]=$i;
                if(in_array($data[$id]['id'],$pid)){
                    $arr[$id][]='<a class="btn btn-link" style="text-decoration:none" onclick="category_son(\'查看子分类\',\'/admin/category/index?id='.$data[$id]['id'].'&pname='.$data[$id]['name'].'\',)" href="javascript:;" title="查看子分类">'.$data[$id]['name'].'</a> 
';

                }else{
                    $arr[$id][]='<a class="btn btn-link" style="text-decoration:none" >'.$data[$id]['name'].'</a> 
';
                }

                $arr[$id][]=$data[$id]['listorder'];
                $arr[$id][]=$data[$id]['create_time'];
                $arr[$id][]=$data[$id]['status'];
                if(in_array($data[$id]['id'],$pid)){//顶级分类判断
                    if(in_array($data[$id]['id'],$phid)){//是否有子分类判断
                        $arr[$id][]='<td class="f-14 td-manage">
                                     <a class="btn btn-link" style="text-decoration:none"  href="/admin/category/index?id='.$data[$id]['id'].'&pname='.$data[$id]['name'].'"  title="查看子分类">查看子分类</a> 
                                     <a style="text-decoration:none" class="ml-5"  href="/admin/category/edit?id='.$data[$id]['id'].'" ><i class="Hui-iconfont"></i></a> 
                                     <a style="text-decoration:none" class="ml-5" onclick="category_del(this,'.$data[$id]['id'].')" href="javascript:;" title="删除"><i class="Hui-iconfont"></i></a>
                                     <a style="text-decoration:none" class="ml-5" href="/admin/category/add?id='.$data[$id]["id"].'" ><i class="Hui-iconfont Hui-iconfont-add"></i></a> 
                                     </td>';
                         }else{
                        $arr[$id][]='<td class="f-14 td-manage">
                                      <a class="btn btn-link" style="text-decoration:none"  >无子分类</a> 
                                      <a style="text-decoration:none" class="ml-5" href="/admin/category/edit?id='.$data[$id]['id'].'"  title="编辑"><i class="Hui-iconfont"></i></a> 
                                      <a style="text-decoration:none" class="ml-5" onclick="category_del(this,'.$data[$id]['id'].')" href="javascript:;" title="删除"><i class="Hui-iconfont"></i></a>
                                      <a style="text-decoration:none" class="ml-5" href="/admin/category/add?id='.$data[$id]["id"].'" ><i class="Hui-iconfont Hui-iconfont-add"></i></a> 
                                      </td>';

                    }


                }else{//子分类编辑栏：无添加与查看子分类按钮
                    $arr[$id][]='<td class="f-14 td-manage">
                <a style="text-decoration:none" class="ml-5" href="/admin/category/edit?id='.$data[$id]['id'].'"  title="编辑"><i class="Hui-iconfont"></i></a> 
                <a style="text-decoration:none" class="ml-5" onclick="category_del(this,'.$data[$id]['id'].')" href="javascript:;" title="删除"><i class="Hui-iconfont"></i></a>
                </td>';
                }

 $i++;
        }

        //$data=json_encode($arr,JSON_UNESCAPED_UNICODE);



        $res=array(
            'draw'=>8,
            "recordsTotal"=> 57,
            "recordsFiltered"=> 57,
            'data'=>$arr
        );
        $res=json_encode($res,JSON_UNESCAPED_UNICODE);
//        $res='{
//  "draw": 8,
//  "recordsTotal": 57,
//  "recordsFiltered": 57,
//  "data": [
//    '.$data.'
//  ]
//}';
        echo $res;
    }


    /**
     * 添加无保存
     * @return mixed
     * @throws Exception
     */
    public function add(){
        $pid=0;
        $id=input('get.id');
        if($id){
            $pid=$id;
        }


        if(redis()->exists('顶级分类id和name')){

            $data=redis()->hGetAll('顶级分类id和name');
            $data[0]="顶级分类";

        }else{

            throw new Exception('redis数据出错，key：顶级分类id和name');
        }


        return $this->fetch('',[
            'parent'=>$data,
            'pid'=>$pid,
        ]);
    }

    public function edit(){
        $id=input('get.id');
        if(!$id){
            $this->redirect('/404.html');
        }
      //  $id=1;
        $data=Db::table('o2o_category')->where('id',$id)->find();
      //  halt($data['status']);
        return $this->fetch('',[
            'data'=>$data,

        ]);
    }

    public function save(){
        $data=input('param.');

        $id=input('get.id');
        $res=0;

      //  mylog($data);
        if($id){//修改内容有id参数
            $res=model('category')->save($data,['id'=>$id]);
            (new Redisdata())->categoryAll();//刷新redis数据
            (new Redisdata())->categoryP();//刷新redis数据


        }else{//添加内容无id参数

            $data['status']=1;
            $data['create_time']=time();

          //  mylog($data);exit;
            $res=Db::table('o2o_category')->insertGetId($data);//@return id号
            (new Redisdata())->categoryP();//刷新redis数据
            (new Redisdata())->categoryAll();//刷新redis数据

        }

        if($res==0){
            $resdata = $this->result('','1','');
        }else{
            $resdata = $this->result('','1','');
        }




        return $resdata;

    }

    /*
     * ajax验证，暂时用不上给了true：百度糯米网站逻辑上是可以重名的
     */
    public function nameV(){
        $data=input('param.');
        mylog($data);
        return true ;

    }

    public function del($id){
        $res=model('category')->save([
            'status'=>-1,
            'update_time'=>time(),
        ],['id'=> $id]);

        if($res){
            $data=[
                'status'=>1,
                'msg'=>'已删除'
            ];
            (new Redisdata())->categoryAll();

        }else{
            $data=[
                'status'=>0,
                'msg'=>'删除失败'
            ];
        }
        return $data;

    }

}