<?php

namespace app\admin\controller;

use app\common\model\ProductCate as ModelProductCate;
use app\admin\controller\Base;
use think\facade\Request;
use app\common\controller\Tool;

class ProductCate extends Base
{
    //列表页面显示分类
    public function getList()
    {
        $pagenum =  \trim(request()->param('pagenum'));
        $pagesize = \trim(request()->param('pagesize'));
        $query = \trim(request()->param('query'));
        if (empty($pagenum) || empty($pagesize)) {
            return \show(config('status.error'), '传输数据为空', null);
        }

        $procateObj = new ModelProductCate();
        $tool = new Tool();
        $res = $procateObj->getProCateList($query)->toArray();
        $res  = $tool->tree($res,0);
        $Total = count($res);
        $data=array();
      if($pagesize>$Total){
        $data=$res;
      }else{
        for($i=$pagesize*($pagenum-1);$i<$pagesize*$pagenum;$i++){
            array_push($data,$res[$i]);
       }
      }
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $data, $Total);
    }

    //获取所有分类
    public function getParentList()
    {
        $id =  \trim(request()->param('id'));
        $procateObj = new ModelProductCate();
        $tool = new Tool();
        $res = $procateObj->select();
        $res  = $tool->tree($res,0);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }
    

    public function add0()
    {
        $procate = Request::param();
        $procateObj = new ModelProductCate();
        $res = $procateObj->save($procate);
        $procateObjUp = $procateObj::find($procateObj->id);
        $strid = \strval($procateObj->id);
        $procateObjUp->family =  $strid.',';
        $res=$procateObjUp->save();
        if (!$res) {
            return show(config('status.error'), '更新失败', $res[]);
        }
        return show(config('status.success'), '更新成功', $res);
    }

    public function add()
    {
        $procate = Request::param();
        $procateObj = new ModelProductCate();
        $res=$procateObj->save($procate);
        $procateObjUp = $procateObj::find($procateObj->id);
        $parentObj=$procateObj->find($procate['pid'])->toArray();
        $strid = \strval($procateObj->id).',';
        $procateObjUp->family = $parentObj['family'].$strid;
        $res=$procateObjUp->save();
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }


    public function getProCate()
    {
        $id =  \trim(request()->param('id'));
        $procateObj = new ModelProductCate();
        $res = $procateObj->where('id',$id)->find();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }

    public function edit()
    {
        $procate = Request::param();
        $procateObj = new ModelProductCate();
        if($procate['pid']==0){
            $res = $procateObj->updateById($procate['id'], $procate);
            if(empty($res)){
                return show(config('status.error'), '更新失败1', null);
            }
            return show(config('status.success'), '更新成功', null);
        }
        $parent = $procateObj::find($procate['pid'])->toArray();
        $strid = \strval($procate['id']).',';
        $procate['family'] = $parent['family'].$strid;
        $res = $procateObj->updateById($procate['id'], $procate);
        if (!$res) {
            return show(config('status.error'), '更新失败2', null);
        }
        return show(config('status.success'), '更新成功', null);
    }

    public function remove()
    {
        $id = Request::param('id');
        $procateObj =  new ModelProductCate();
        $res = $procateObj->remove($id);
        if (empty($res)) {
            return show(config('status.error'), '删除失败', null);
        }
        return show(config('status.success'), '删除成功', null);
    }

    
  

}
