<?php
namespace app\index\controller;
use app\BaseController;
use app\common\model\User;

class Base extends BaseController
{

    public function initialize()
    {
        $token = \request()->header('Authorization');
        $this->checkToken($token);
    }

    //用于检验 token 是否存在, 并且更新 
    public function checkToken($token)
    {
        $res0=[];
        $userObj = new User();
        $res = $userObj->where('token',$token)->find();
        if(empty($res)){
            $res0 = [
                'status' => 0,
                'message' => '没有token,请先登录！',
                'result' => null,
            ];
            json($res0)->send();exit;//没有token,验证失败
        }

        if(time()-$res['token_out']>0){
            $res0 = [
                'status' => 0,
                'message' => 'token过期，请重新登录！',
                'result' => null,
            ];
            json($res0)->send();exit;
        }
    }

  
}
