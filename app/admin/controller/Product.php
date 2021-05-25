<?php

namespace app\admin\controller;

use app\common\model\Product as ModelProduct;
use app\admin\controller\Base;
use app\common\model\Sku;
use think\facade\App;
use think\facade\Db;
use think\facade\Request;

class Product extends Base
{

    public function getList()
    {
        $cate_id =  \trim(request()->param('cate_id'));
        $pagenum =  \trim(request()->param('pagenum'));
        $pagesize = \trim(request()->param('pagesize'));
        $query = \trim(request()->param('query'));
        if (empty($pagenum) || empty($pagesize)) {
            return \show(config('status.error'), '传输数据为空', null);
        }
        $proObj = new ModelProduct();
        $res = $proObj->getList($cate_id, $pagenum, $pagesize, $query)->toArray();
        $Total = $proObj->getTotal($query,$cate_id);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res, $Total);
    }

    public function getCount()
    {
        $proObj = new ModelProduct();
        $res = $proObj->select()->count();
        return show(config('status.success'), '查询数据成功', $res);
    }

    public function add()
    {
        $param = Request::param();
        $product = $param['product'];
        $skuList = $param['skuList'];
        $sku = [];
        $product['date'] = date('Y-m-d H:i:s', strtotime($product['date']));
        $proObj = new ModelProduct();
        $resPro = $proObj->save($product);
        if (!$resPro) {
            return show(config('status.error'), '基本信息添加失败', $resPro);
        } 
        $count = Db::table('specs')->count();
        foreach($skuList as $key=>$vo){
              for($i=0;$i<$count;$i++){
                 $sku[$key]['specs_value_id'][$i]=$vo[$i];
              }
            $sku[$key]['specs_value_id']=\implode(',',$sku[$key]['specs_value_id']);
            $sku[$key]['product_id']=$proObj->id;
            $sku[$key]['price']=$skuList[$key]['price'];
            $sku[$key]['const_price']=$skuList[$key]['const_price'];
            $sku[$key]['stock']=$skuList[$key]['stock'];
        }
        $res =  Db::table('sku')->insertAll($sku);
        if ($res) {
            return show(config('status.success'), '添加成功', $res);
        } else {
            return show(config('status.nosku'), '内容添加成功但sku没有入库', null);
        }
    }

    public function getPro()
    {
        $id =  \trim(request()->param('id'));
        $proObj = new ModelProduct();
        $res = $proObj->where('id',$id)->find();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }

    public function edit()
    {
        $product = Request::param();
        $from['date'] = date('Y-m-d H:i:s', strtotime($product['date']));
        $proObj = new ModelProduct();
        $res = $proObj->updateById($product['id'], $product);
        if (!$res) {
            return show(config('status.error'), '没有修改任何数据', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }

    public function editWithSku()
    {
        $param = Request::param();
        $product = $param['product'];
        $skuList = $param['skuList'];
        $skuObj = new Sku();
        $proObj = new ModelProduct();
        $del = $skuObj->where('product_id',$product['id'])->delete();
        $sku = [];
        $count = Db::table('specs')->count();
        foreach($skuList as $key=>$vo){
              for($i=0;$i<$count;$i++){
                 $sku[$key]['specs_value_id'][$i]=$vo[$i];
              }
            $sku[$key]['specs_value_id']=\implode(',',$sku[$key]['specs_value_id']);
            $sku[$key]['product_id']=$product['id'];
            $sku[$key]['price']=$skuList[$key]['price'];
            $sku[$key]['const_price']=$skuList[$key]['const_price'];
            $sku[$key]['stock']=$skuList[$key]['stock'];
        }
        $resSku =  Db::table('sku')->insertAll($sku);
        if (empty($resSku)) {
            return show(config('status.error'), 'sku入库失败', $resSku);
        } 
        $from['date'] = date('Y-m-d H:i:s', strtotime($product['date']));
        $res = $proObj->updateById($product['id'], $product);
        if (!$res) {
            return show(config('status.error'), '没有修改任何数据', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }


    public function remove()
    {
        $id = Request::param('id');
        $proObj = new ModelProduct();
        $skuObj = new Sku();
        $del = $skuObj->where('product_id',$id)->delete();
        $res = $proObj->where('id',$id)->delete();
        if (empty($res)) {
            return show(config('status.error'), '删除失败', $res);
        }
        return show(config('status.success'), '删除成功', $res);
    }


    public function uploadImg()
    {
        $dir =App::getRootPath().'/public/upload/product/';
        if (empty($_FILES['file'])) {
            return show(config('status.error'), '没有接收到file对象信息', null);
        };
        if ($_FILES['file']['error'] > 0) {
            return show(config('status.error'), '错误:' . $_FILES['file']['error'], null);
        };
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $_FILES['file']['name'] = \time_rand() . '.' . \get_extension($_FILES['file']['name']);

        // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
        move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $_FILES["file"]["name"]);
        $res = [
            'name' => $_FILES['file']['name'],
            'url' => 'http://'.$_SERVER['SERVER_NAME'].'/upload/product/'.$_FILES["file"]["name"],

        ];
        return show(config('status.success'), '上传成功', $res);
    }

    public function editUpload()
    {
        $dir =App::getRootPath().'/public/upload/product/';
        if (empty($_FILES['file'])) {
            return show(config('status.error'), '没有接收到file对象信息', null);
        };
        if ($_FILES['file']['error'] > 0) {
            return show(config('status.error'), '错误:' . $_FILES['file']['error'], null);
        };
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $_FILES['file']['name'] = \time_rand() . '.' . \get_extension($_FILES['file']['name']);

        // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
        move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $_FILES["file"]["name"]);
        $res = [
            'location' => 'http://'.$_SERVER['SERVER_NAME'].'/upload/product/'.$_FILES["file"]["name"],

        ];
       
        return json_encode($res);
    }

    public function changeStatus()
    {
        $id = trim(request()->param('id'));
        $status = trim(request()->param('status'));
        $proObj = new ModelProduct();
        $res = $proObj->updateStatusByid($id, $status); //返回0或1
        if (!$res || empty($res)) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }



}
