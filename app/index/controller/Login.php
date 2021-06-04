<?php

namespace app\index\controller;
use app\BaseController;
use app\common\model\User;

class Login extends BaseController
{
    public function check()
    {
        $username = request()->param('username');
        $password = request()->param('password');
        if(empty($username)||empty($password)){
            return show(config('status.error'),'用户名或密码为空',null);
        }
        
        $userObj = new User();
        $user=$userObj->getUserByusername($username); 
        if(empty($user)){
            return show(config('status.error'),'没有该用户',null);
        }

        $user=$user->toArray();
        if($user['status']!==1){
            return show(config('status.error'),'用户状态为0',null);
        }

        //判断密码是否正确
        if($user['password']!==passwordMd5($password)){    
            return show(config('status.error'),'密码错误！',null);
        }

        //正确之后用token保存前端session状态
        $token = makeToken();
        $token_out = strtotime("+7 days");
        $userinfo = ['token_out' => $token_out,'token' => $token];
        $res = $userObj->updateUserByusername($username,$userinfo);
        if($res){
            return show(config('status.success'),'登录成功！',$res);
        }else{
            return show(config('status.error'),'登录失败！token更新出错！',null);
        }
    }

}
