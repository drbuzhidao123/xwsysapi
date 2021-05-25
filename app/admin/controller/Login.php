<?php
namespace app\admin\controller;
use app\BaseController;
use app\common\model\Admin;
use app\common\model\AuthGroup;
use app\common\model\AuthGroupAccess;

class Login extends BaseController
{
    public function check()
    {
        $username = request()->param('username');
        $password = request()->param('password');
        if(empty($username)||empty($password)){
            return show(config('status.error'),'用户名或密码为空',null);
        }
        
        $adminObj = new Admin();
        $admin=$adminObj->getUserByusername($username);
        if(empty($admin)){
            return show(config('status.error'),'没有该用户',null);
        }

        $admin=$admin->toArray();
        if($admin['status']!==1){
            return show(config('status.error'),'用户状态为0',null);
        }

        //判断密码是否正确
        if($admin['password']!==passwordMd5($password)){
            return show(config('status.error'),'密码错误！',null);
        }

        //正确之后用token保存前端session状态
        $token = makeToken();
        $token_out = strtotime("+7 days");
        $userinfo = ['token_out' => $token_out,'token' => $token];
        $res = $adminObj->updateUserByusername($username,$userinfo);
        if($res){
            $accessObj = new AuthGroupAccess();
            $groupObj = new AuthGroup();
            $access =  $accessObj->where('uid',$res['id'])->find()->toArray();
            $group =  $groupObj->where('id',$access['group_id'])->field('title')->find()->toArray();
            $res['group']=$group['title'];
            return show(config('status.success'),'登录成功！',$res);
        }else{
            return show(config('status.error'),'登录失败！token更新出错！',null);
        }
    }

     //用于检验 token 是否存在, 并且更新 token
     /*public function checkToken($token)
     {
         $userObj = new User();
         $res = $userObj->field('token_out')->where('token', $token)->select();
         if (!empty($res)) {
             if (time() - $res[0]['time_out'] > 0) {
                 return show(config('status.token_out'),'token过期！',null); //token长时间未使用而过期，需重新登陆
             }
             $new_time_out = time() + 604800; //604800是七天
             $res = $userObj->isUpdate(true)
                 ->where('token', $token)
                 ->update(['token_out' => $new_time_out]);
             if ($res) {
                 return show(config('status.success'),'token验证成功！',null); //token验证成功，time_out刷新成功，可以获取接口信息
             }
         }
         return show(config('status.error'),'没有token或验证失败！',null); //token错误验证失败
     }*/

     public function getadminpassword()
     {
        $password = \passwordMd5(123);
        return $password;
     }

}
