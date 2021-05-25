<?php

namespace app\admin\controller;
use app\admin\controller\Base;
use app\common\model\SpecsValue as ModelSpecsValue;
use think\facade\Request;

class SpecsValue extends Base
{
    public function getList()
    {
        $pagenum =  \trim(request()->param('pagenum'));
        $pagesize = \trim(request()->param('pagesize'));
        $query = \trim(request()->param('query'));
        $specs_id = \trim(request()->param('specs_id'));
        if (empty($pagenum) || empty($pagesize)) {
            return \show(config('status.error'), '传输数据为空', null);
        }

        $SpecsValueObj = new ModelSpecsValue();
        $res = $SpecsValueObj->getList($pagenum, $pagesize, $query, $specs_id)->toArray();
        $Total = $SpecsValueObj->getTotal($query);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res, $Total);
    }

    public function add()
    {
        $specsValue = Request::param();
        $SpecsValueObj = new ModelSpecsValue();
        $res = $SpecsValueObj->save($specsValue);
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }


    public function getbyId()
    {
        $id =  \trim(request()->param('id'));
        $SpecsValueObj = new ModelSpecsValue();
        $res =  $SpecsValueObj->where('id',$id)->find()->toArray();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }

    public function edit()
    {
        $specs = Request::param();
        $SpecsValueObj = new ModelSpecsValue();
        $res =$SpecsValueObj->updateById($specs['id'], $specs);
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }

    public function remove()
    {
        $id = Request::param('id');
        $SpecsValueObj = new ModelSpecsValue();
        $res =$SpecsValueObj->where('id',$id)->delete();
        if (empty($res)) {
            return show(config('status.error'), '删除失败', $res);
        }
        return show(config('status.success'), '删除成功', $res);
    }
  

}
