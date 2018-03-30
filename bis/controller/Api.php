<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2018/3/12
 * Time: 11:32
 */

namespace app\bis\controller;


use think\Controller;
use think\Db;
use think\Request;

class Api extends Controller
{
    //该控制器是与web页面交互的ajax数据接口


    /*
     * 城市：选择一级分了自动带出二级分类效果接口
     * @param $pid post传参，一级分了id即是二级分类父id
     * @return json  result方法为tp5 自带
     */
    public function city(){
        $pid=input('param.pid');

        $data=Db::table('o2o_city')->where('parent_id',$pid)->where('status',1)->select();
        if($data->isEmpty()){
            return $this->result($data,2,'无数据');
        }
        return $this->result($data,1,'success');

    }

    /*
 * 分类：选择一级分了自动带出二级分类效果接口
 * @param $pid post传参，一级分了id即是二级分类父id
 * @return json  result方法为tp5 自带
 */
    public function category(){
        $pid=input('param.pid');

        $data=Db::table('o2o_category')->where('parent_id',$pid)->where('status',1)->select();
        if($data->isEmpty()){
            return $this->result($data,2,'无数据');
        }
        return $this->result($data,1,'success');

    }


    /*
     * 图片回调显示接口
     * @param file
     * @return url 图片的url地址
     */
    public function image(){
//        $data=input('param.');
//        mylog($data);
        $file = Request::instance()->file('file');

        // 给定一个目录
        $info = $file->move('static/images');

        if($info){
            $url=$info->getSaveName();

            mylog($url);

       $res=['comment'=>$url,'code'=>1];
        }else{
// 上传失败获取错误信息
            $error=$info->getError();
          mylog($error);
          $res= ['comment'=>$error,'code'=>0];
        }
        return json($res);

    }


    /**
     * 名称验证
     * @param name
     * @return bool|string
     */
    public function nameV(){
        $name=input('post.name');
       $res=Db::table('o2o_bis')->where('name','like',$name)->value('id');
       if(empty($res)){
           return true;
       }else{
           return '已存在相同名称的商户';//不传false的话插件直接提示此字符串
       }

    }


    //因为写了权限体系，各控制器及其删除与更改状态属于不同权限，所以每个控制器写有单独的如下一样代码的方法，那不叫冗余
    public function status(){
        $res['code']=1;
        $data=input('post.');
        $status=$data['status'];
        $id=$data['id'];
        $model=$data['model'];

        if(!is_numeric($status) || !is_numeric($id) || !$model){
            $res['msg']='参数出错，请检查js代码';
            $res['code']=0;
            return $res;
        }

        $data=model($model)->save(['status'=>$status],['id'=>$id]);

        if($data!==1){
            $res['code']=0;
            $res['msg']='后端业务故障，数据未成功保存';
            return $res;
        }

        return $res;
    }

}