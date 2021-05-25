<?php

namespace app\admin\controller;

//use app\common\model\AuthGroup;
use app\common\model\AuthGroupAccess;
use app\common\model\Admin as ModelAdmin;
use app\admin\controller\Base;
use think\facade\Request;
use think\facade\Db;

class Admin extends Base
{
    public function getList()
    {
        $pagenum =  \trim(request()->param('pagenum'));
        $pagesize = \trim(request()->param('pagesize'));
        $query = \trim(request()->param('query'));
        if (empty($pagenum) || empty($pagesize)) {
            return \show(config('status.error'), '传输数据为空', null);
        }

        $adminObj = new ModelAdmin();
        $res = $adminObj->getUserList($pagenum, $pagesize, $query)->toArray();
        $Total = $adminObj->getUserTotal($query);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res , $Total);
    }

    public function List()
    {
        $res=Db::table('Admin')
        ->alias('u')
        ->leftjoin('auth_group_access a','u.id = a.uid')
        ->leftjoin('auth_group g','a.group_id = g.id')
        ->field('u.id,u.username,u.password,u.status,u.created,u.updated,u.logined,u.token,u.token_out,u.mobile,a.group_id,g.title')
        ->select();
        dump($res);
    }

    public function getUser()
    {
        $id =  \trim(request()->param('id'));
        if (empty($id)) {
            return \show(config('status.error'), '传输数据为空', null);
        }
        $adminObj = new ModelAdmin();
        $res =$adminObj->getUserById($id)->toArray();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }

    /*public function getUserwithGroup()
    {
        $id =  \trim(request()->param('id'));
        if (empty($id)) {
            return \show(config('status.error'), '传输数据为空', null);
        }
        $accessObj = new AuthGroupAccess();
        $groupObj = new AuthGroup();
        $access =  $accessObj->where('uid',$id)->find()->toArray();
        $group =  $groupObj->where('id',$access['group_id'])->field('title')->find()->toArray();
        $userObj = new ModelUser();
        $res = $userObj->getUserById($id)->toArray();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        $res['group'] = $group['title'];
        return show(config('status.success'), '查询数据成功', $res);
    }*/

    /*public function getTotal()
    {
        $userObj = new ModelUser();
        $Total = $userObj->getUserTotal();
        return $Total;
        return  show(config('status.success'), '查询数据成功', $Total);
    }*/

    public function changeStatus()
    {
        $userid = trim(request()->param('userid'));
        $status = trim(request()->param('status'));
        $adminObj = new ModelAdmin();
        $res = $adminObj->updateStatusByid($userid, $status); //返回0或1
        if (!$res || empty($res)) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }


    public function add()
    {
        $user = Request::param();
        $adminObj = new ModelAdmin();
        $user['password'] = passwordMd5($user['password']);
        $res = $adminObj->save($user); //返回boolse值
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }

    public function edit()
    {
        $user = Request::param();
        $adminObj = new ModelAdmin();
        //$user['password'] = \passwordMd5($user['password']);
        $res = $adminObj->updateById($user['id'], $user);
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        return show(config('status.success'), '更新成功', $res);
    }

    public function remove()
    {
        $id = Request::param('id');
        $adminObj = new ModelAdmin();
        $accessObj = new AuthGroupAccess();
        $access =  $accessObj->where('uid',$id)->find();
        if(!empty($access)){
            $res0=$accessObj->where('uid',$id)->delete();
            if(empty($res0)){
                return show(config('status.error'), '用户明细表删除失败', $res0);
            }
           }
        $res = $adminObj->where('id',$id)->delete();
        if (empty($res)) {
            return show(config('status.error'), '删除失败', $res);
        }
        return show(config('status.success'), '删除成功', $res);
    }

    public function setGroup()
    {
        $group = Request::param();
        $AccessObj = new AuthGroupAccess();
        unset($group['title']);
        $access = $AccessObj::where('uid',$group['uid'])->find();
        if(!empty($access)){
            $res = $access->save($group);
        }else{
            $res = $AccessObj->save($group);
        }
        if (!$res) {
            return show(config('status.error'), '更新失败', null);
        }
        return show(config('status.success'), '更新成功', null);
        
    }


}
