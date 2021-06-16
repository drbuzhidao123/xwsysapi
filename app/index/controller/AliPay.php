<?php

namespace app\index\controller;

use app\BaseController;
use aop\AopClient;
use aop\request\AlipayTradePagePayRequest;
use app\common\model\Order;
use think\facade\Config;
use think\facade\View;

class AliPay extends BaseController
{

    public function pagePay()
    {
        $aop = new AopClient();
        $order_id =  request()->param('order_id');
        $order_body =  request()->param('order_body');;
        $total_price =  request()->param('total_price');
        $aop->gatewayUrl = Config::get('alipay.gatewayUrl');
        $aop->appId =  Config::get('alipay.app_id');
        $aop->method = 'alipayTradePagePay';
        $aop->charset = 'utf-8';
        $aop->signType = 'RSA2';
        $aop->alipayrsaPublicKey = Config::get('alipay.ali_public_key');
        $aop->rsaPrivateKey      = Config::get('alipay.private_key');
        $biz_content = json_encode([
            'out_trade_no' => $order_id,
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
            'total_amount' => $total_price,
            'subject' =>  $order_body,
            'timeout_express' => '30m',
        ]);

        $payRequest = new AlipayTradePagePayRequest();

        $payRequest->setReturnUrl(Config::get('alipay.return_url'));
        $payRequest->setNotifyUrl(Config::get('alipay.notify_url'));
        $payRequest->setBizContent($biz_content);
        $response = $aop->pageExecute($payRequest);
        //$result = $aop->pageExecute($payRequest,"GET");
        return show(config('status.success'),'支付跳转', $response);
        //return $response;
    }

    public function notify()
    {
        $post = $_POST;
        $result = $this->check($post);
        if ($result) {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号
            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];

            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //即时到账普通版，代表交易完全完成，支付宝之后不会再发送请求到这里
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //即时到账高级版，这个版本用户可以退款，在一定时间内交易状态会自动变成TRADE_FINISHED
                $orderObj = new Order();
                $res = $orderObj->updateOnPayClose($out_trade_no,2);
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";	

        } else {
            //验证失败
            echo "fail";
        }
    }

    public function  return()
    {
        $get = $_GET;
        $result = $this->check($get);
        if ($result) {
            //商户订单号
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);
            //支付宝交易号
            $trade_no = htmlspecialchars($_GET['trade_no']);
            View::assign([
                'order_id'  => $out_trade_no,
                'ali_order_id' => $trade_no,
            ]);
            return View::fetch();
        } else {
            return View::fetch('eror');
        }
    }


    public function check($get)
    {
        $aop = new AopClient();
        $aop->alipayrsaPublicKey = Config::get('alipay.ali_public_key');
        $result = $aop->rsaCheckV1($get, Config::get('alipay.ali_public_key'), 'RSA2');
        return $result;
    }
}
