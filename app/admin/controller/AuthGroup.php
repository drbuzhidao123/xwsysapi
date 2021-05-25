<?php

namespace app\admin\controller;
use app\common\model\AuthGroup as ModelAuthGroup;
use app\common\model\AuthGroupAccess;
use app\common\model\AuthRule;
use app\admin\controller\Base;
use think\facade\Request;

class AuthGroup extends Base
{
    public function getList()
    {
        $pagenum =  \trim(request()->param('pagenum'));
        $pagesize = \trim(request()->param('pagesize'));
        $query = \trim(request()->param('query'));
        if (empty($pagenum) || empty($pagesize)) {
            return \show(config('status.error'), '传输数据为空', null);
        }

        $authObj = new ModelAuthGroup();
        $res = $authObj->getAuthGroupList($pagenum, $pagesize, $query)->toArray();
        $Total = $authObj->getAuthGroupTotal($query);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res, $Total);
    }

    public function add()
    {
        $group = Request::param();
        $groupObj = new ModelAuthGroup();
        /*$rules = array_reduce($group['RuleList'], function ($result, $value) {
            return array_merge($result, array_values($value));
        }, array());*/
        
        $group['rules']=implode(",",$group['rules']);
        $res = $groupObj->save($group);
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }


    public function getGroup()
    {
        $id =  \trim(request()->param('id'));
        $authGroupObj = new ModelAuthGroup();
        //$authObj = new AuthRule();
        $res = $authGroupObj->where('id',$id)->find()->toArray();
        $res['rules']=explode(",", $res['rules']);
        //$auth=$authObj::select($rules);
        //$res['ruleList'] = $auth;
        //unset($res['rules']);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }

    public function edit()
    {
        $group = Request::param();
        $authGroupObj = new ModelAuthGroup();
       //return show(config('status.error'), '更新失败', $group);
        $res = $authGroupObj->updateById($group['id'], $group);
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }

    public function remove()
    {
        $id = Request::param('id');
        $authGroupObj = new ModelAuthGroup();
        $accessObj = new AuthGroupAccess();
        $access =  $accessObj->where('group_id',$id)->find();
        if(!empty($access)){
            $res0=$accessObj->where('group_id',$id)->delete();
            if(empty($res0)){
                return show(config('status.error'), '用户明细表删除失败', $res0);
            }
           }
        $res = $authGroupObj->where('id',$id)->delete();
        if (empty($res)) {
            return show(config('status.error'), '删除失败', $res);
        }
        return show(config('status.success'), '删除成功', $res);
    }
  

}
