<?php
namespace app\admin\controller;
use app\BaseController;
use app\common\model\Admin;
use liliuwei\think\Auth;

class Base extends BaseController
{

    public function initialize()
    {
       /* //cors方式解决跨域
        header("Access-Control-Allow-Origin:*");    //允许访问的来源域名
        header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS');    //响应类型: post,get等 允许的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type,Authorization');    //响应头设置 */
        $token = \request()->header('Authorization');
        //json($token)->send();exit;
        $this->checkToken($token);
        $this->checkAuth($token);   
    }

    //用于检验 token 是否存在, 并且更新 
    public function checkToken($token)
    {
        $res0=[];
        $adminObj = new Admin();
        $res = $adminObj->where('token',$token)->find();
        if(empty($res)){
            $res0 = [
                'status' => 0,
                'message' => '没有token,验证失败',
                'result' => null,
            ];
            json($res0)->send();exit;//没有token,验证失败
        }

        if(time()-$res['token_out']>0){
            $res0 = [
                'status' => 0,
                'message' => 'token过期，请重新登录',
                'result' => null,
            ];
            json($res0)->send();exit;
            //return show(config('status.token_out','token过期，请重新登录',null));
        }
    }

    public function checkAuth($token){
        $res0=[];
        $adminObj = new Admin();
        $res = $adminObj->where('token',$token)->find();
        $auth = new Auth();
        $controller = strtolower(request()->controller());
        //$action = strtolower(request()->action());
        $url = $controller;
        if (!$auth->check($url,$res['id'])) {
            $res0 = [
                'status' => 0,
                'message' => '对不起，你没有权限',
                'result' => null,
            ];
            json($res0)->send();exit;
         } 

    }
  
}
