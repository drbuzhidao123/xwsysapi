<?php

namespace app\index\controller;

use app\BaseController;
use app\common\model\Product as ModelProduct;

class Product extends BaseController
{
    //带分页数据
    public function getList()
    {
        $cate_id =  \trim(request()->param('cate_id'));
        $pagenum =  \trim(request()->param('pagenum'));
        $pagesize = \trim(request()->param('pagesize'));
        if (empty($pagenum) || empty($pagesize)) {
            return \show(config('status.error'), '没有pagenum或pagesize', null);
        }
        $proObj = new ModelProduct();
        $res = $proObj->getList_home($cate_id, $pagenum, $pagesize);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }

    public function getRecommend()
    {
        $cate_id =  \trim(request()->param('cate_id'));
        $limit =  \trim(request()->param('limit'));
        if(empty($cate_id)){
            return \show(config('status.error'), '没有分类id', null);
        }
        $proObj = new ModelProduct();
        $res = $proObj->getRecommend($cate_id, $limit)->toArray();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '获取推荐数据成功', $res);
    }

    public function getData()
    {
        $id =  \trim(request()->param('id'));
        $proObj = new ModelProduct();
        $res = $proObj->where('id',$id)->find();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }

  

}
