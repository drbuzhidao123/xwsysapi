<?php

namespace app\index\controller;

use app\BaseController;
use app\common\model\ProductCate as ModelProductCate;
use app\common\controller\Tool;

class ProductCate extends BaseController
{
    //获取所有分类
    public function getAll()
    {
        $procateObj = new ModelProductCate();
        $tool = new Tool();
        $res = $procateObj->select();
        $res  = $tool->tree($res,0);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }
    
    //根据id获取分类
    public function getData()
    {
        $id =  \trim(request()->param('id'));
        $procateObj = new ModelProductCate();
        $res = $procateObj->where('id',$id)->find();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }

}
