<?php
namespace app\index\controller;

use app\common\model\User as ModelUser;
use app\index\controller\Base;

class User extends Base
{
    public function edit()
    {
        $data = request()->param('ruleForm');
        if(empty($data)){
            return show(config('status.error'),'传递的参数为空',null);
        }
        $userObj = new ModelUser();
        $res = $userObj->updateById($data['userId'], $data);
        if (!$res) {
            return show(config('status.error'), '更新失败', $res);
        }
        $res = $userObj->where('id',$data['userId'])->find();
        return show(config('status.success'), '更新成功', $res);
        
    }

  
}
