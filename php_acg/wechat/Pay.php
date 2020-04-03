<?php

namespace app\index\controller;

require_once __DIR__ . "/../../../extend/wx/lib/WxPay.Api.php";
require_once __DIR__ . "/../../../extend/wx/lib/WxPay.Data.php";
require_once __DIR__ . "/../../../extend/wx/pay/WxPay.JsApiPay.php";

use think\Controller;
use think\facade\Config;
use think\Request;

class Pay extends Controller
{
    
    protected $app_id = 'you_app_id';
    protected $app_secret = 'you_secret';

    public function getCode()
    {
        // 获取code
        $redirect_url = urlencode('http://192.168.1.23:6002/wechat/getopenid');
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_url}&response_type=code&scope=snsapi_userinfo&state=typemoon#wechat_redirect";
        return redirect($url);
    }

    public function getOpenid(Request $requets)
    {
        // 获取code
        $get = $requets->get();
        if (empty($get['code'])) return json(['code' => 5001, 'mess' => 'Code Lost!!', 'data' => []]);
        
        // 获取openid
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$get['code']}&grant_type=authorization_code";
        $requets = HttpSend::get($url);
        if ($requets['http_code'] != 200) return false;

        $body = json_decode($requets['body'], true);
        if ($body == false || empty($body['openid']) || empty($body['access_token'])) return json(['code' => 5001, 'mess' => 'Get info fail!!', 'data' => []]);
        
        $url_user = "https://api.weixin.qq.com/sns/userinfo?access_token={$body['access_token']}&openid={$body['openid']}&lang=zh_CN";
        $request_user = HttpSend::get($url_user);
        // 解析json
        $user_info = json_decode($requets['body'], true);
        if ($body == false || empty($body['openid'])) return json(['code' => 5001, 'mess' => 'Get user info fail!!', 'data' => []]);
        $data = get_only_keys($user_info, ['openid', 'nickname', 'sex', 'province', 'city', 'country', 'headimgurl', 'unionid']);
        if ($user_info['privilege'] == false) $data['privilege'] = json_encode($user_info['privilege']);

        // 存储用户数据
        $bool = DB::table('wx_users')->insert((array)$data);
        if (!$bool) return json(['code' => 5001, 'mess' => 'save user info fail!!', 'data' => []]);
        unset($data['openid'], $data['unionid']);
        return json(['code' => 2000, 'mess' => 'success', 'data' => ['user_info' => $data]]);
    }

    public function wxPay()
    {
        // 订单数据
        $orderDatas = [
            'title' => 'TypeMoon',
            'order_id' => '1',
            'order_total_price' => '0.01',
            'openid' => 'openid',
            'order_id' => 'order_id',
        ];
        $wx_conf = Config::get('wx');
        // appid
        $conf['weixin_appid'] = $wx_conf['wx_appid'];
        // appsecret
        $conf['weixin_appsecret'] = $wx_conf['wx_app_secret'];
        // 绑定的微信商户号
        $conf['weixin_mchid'] = $wx_conf['wx_open_mchid'];
        // 绑定的微信商户密钥
        $conf['weixin_key'] = $wx_conf['wx_open_key'];

        // $tools = new \JsApiPay();
        $tools = new \JsApiPay();
        // 在此类中配置
        $config = new \WxPayConfig();
    
        // 统一下单
        $input = new \WxPayUnifiedOrder();
    
        // 设置商户分配的唯一32位key
        // \WxPayConfig::$KEY = $conf['weixin_key'];
    
        // 微信分配的公众账号ID
        // $input->SetAppid($conf['weixin_appid']);
        // 微信支付分配的商户号
        // $input->SetMch_id($conf['weixin_mchid']);
        
        // 商品描述
        $input->SetBody($orderDatas['title']);
        // 支付单号
        $input->SetOut_trade_no($orderDatas['order_id']);
        // 设置订单总金额，单位为分，只能为整数
        $input->SetTotal_fee(intval(strval($orderDatas['order_total_price'] * 100)));
        // 交易类型
        $input->SetTrade_type('JSAPI');
        // 接收微信支付异步通知回调地址
        $input->SetNotify_url($config->GetNotifyUrl());
        $input->SetOpenid($orderDatas['openid']);
        $result = \WxPayApi::unifiedOrder($config, $input);
    
        // 判断错误信息
        if ($result['return_code'] !== 'SUCCESS' || $result['result_code'] !== 'SUCCESS') {
            return json(['code' => 5001, 'mess' => $result['return_msg'], 'data' => []]);
        }
        $jsApiParameters = $tools->GetJsApiParameters($result);  // 获取前端需要的参数，用于调起微信支付页面样式

        return json(['code' => 2000, 'mess' => 'success', 'data' => ['user_info' => $jsApiParameters]]);
    }

    
    
    
    
}


/*

<?xml version="1.0" encoding="utf-8"?>

<xml>
  <appid><![CDATA[]]></appid>
  <body><![CDATA[TypeMoon]]></body>
  <mch_id><![CDATA[]]></mch_id>
  <nonce_str><![CDATA[dr9lwp6kol1ondfg3nzubtjnbim80thj]]></nonce_str>
  <notify_url><![CDATA[you_url]]></notify_url>
  <openid><![CDATA[openid]]></openid>
  <out_trade_no><![CDATA[order_id]]></out_trade_no>
  <sign_type><![CDATA[MD5]]></sign_type>
  <spbill_create_ip><![CDATA[127.0.0.1]]></spbill_create_ip>
  <total_fee>1</total_fee>
  <trade_type><![CDATA[JSAPI]]></trade_type>
  <sign><![CDATA[6EBA2032755A0965E86BE3D0A4670E02]]></sign>
</xml>

<?xml version="1.0" encoding="utf-8"?>

<xml>
  <appid><![CDATA[appid_WxPayConfig]]></appid>
  <body><![CDATA[TypeMoon]]></body>
  <mch_id><![CDATA[mchid_WxPayConfig]]></mch_id>
  <nonce_str><![CDATA[m75kihbmcn2jma8hibwfb5kld6z0v7u0]]></nonce_str>
  <notify_url><![CDATA[you_url]]></notify_url>
  <openid><![CDATA[openid]]></openid>
  <out_trade_no><![CDATA[order_id]]></out_trade_no>
  <sign_type><![CDATA[MD5]]></sign_type>
  <spbill_create_ip><![CDATA[127.0.0.1]]></spbill_create_ip>
  <total_fee>1</total_fee>
  <trade_type><![CDATA[JSAPI]]></trade_type>
  <sign><![CDATA[5080092C7E793949BEB3874CD2BF3CF2]]></sign>
</xml>



*/




