<?php

namespace app\admin\controller;
use app\admin\controller\Base;
use app\common\model\Specs as ModelSpecs;
use app\common\model\SpecsValue;
use think\facade\Request;

class Specs extends Base
{
    public function getList()
    {
        $pagenum =  \trim(request()->param('pagenum'));
        $pagesize = \trim(request()->param('pagesize'));
        $query = \trim(request()->param('query'));
        if (empty($pagenum) || empty($pagesize)) {
            return \show(config('status.error'), '传输数据为空', null);
        }

        $specsObj = new ModelSpecs();
        $res = $specsObj->getList($pagenum, $pagesize, $query)->toArray();
        $Total = $specsObj->getTotal($query);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res, $Total);
    }

    public function getSpecs()
    {
        $specsValueObj = new SpecsValue();
        $specsObj = new ModelSpecs();
        $res1 = $specsObj->getList()->toArray();
        $res2 = $specsValueObj->getSpecs($res1);
        return  show(config('status.success'), '查询数据成功', $res2);
    }

    public function add()
    {
        $specs = Request::param();
        $specsObj = new ModelSpecs();
        $res = $specsObj->save($specs);
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }


    public function getbyId()
    {
        $id =  \trim(request()->param('id'));
        $specsObj = new ModelSpecs();
        $res =$specsObj->where('id',$id)->find()->toArray();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }

    public function edit()
    {
        $specs = Request::param();
        $specsObj = new ModelSpecs();
        $res =$specsObj->updateById($specs['id'], $specs);
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }

    public function remove()
    {
        $id = Request::param('id');
        $specsObj = new ModelSpecs();
        $res =$specsObj->where('id',$id)->delete();
        if (empty($res)) {
            return show(config('status.error'), '删除失败', $res);
        }
        return show(config('status.success'), '删除成功', $res);
    }
  

}
