<?php
namespace app\admin\controller;

use app\common\model\AuthRule;
use app\admin\controller\Base;

class Rules extends Base
{
    public function getList()
    {
        $pagenum =  \trim(request()->param('pagenum'));
        $pagesize = \trim(request()->param('pagesize'));
        $query = \trim(request()->param('query'));
        if (empty($pagenum) || empty($pagesize)) {
            return \show(config('status.error'), '传输数据为空', null);
        }

        $authObj = new AuthRule();
        $res = $authObj->getAuthList($pagenum, $pagesize, $query)->toArray();
        $Total = $authObj->getAuthTotal($query);
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res , $Total);
    }

    public function getNameList()
    {
        $authObj = new AuthRule();
        $res = $authObj->field('id,title')->select()->toArray();
        if (empty($res)) {
            return show(config('status.error'), '没有数据', $res);
        }
        return show(config('status.success'), '查询数据成功', $res);
    }
    


  
}
