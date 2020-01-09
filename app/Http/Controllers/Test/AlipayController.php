<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlipayController extends Controller
{
    public function alipay()
    {
        // 支付网关
        $ali_geteway='  https://openapi.alipaydev.com/gateway.do';

    	// 公共请求参数
    	$app_id='2016101100657171';
    	$method='alipay.trade.page.pay';
    	$charset='utf-8';
    	$sign_type='RSA2';
    	$sign='';  // 密钥 商户请求参数的签名串
    	$timestamp=date('Y-m-d H:i:s');
    	$version='1.0';
        $return_url='http://1905dongbaixue.comcto.com/alipay/return';  // 支付宝同步通知
    	$notify_url='http://1905dongbaixue.comcto.com/alipay/notify';  // 支付宝异步通知地址
    	$biz_content='';

    	// 1 请求参数
    	$out_trade_no=time().rand(1111,9999); // 商户订单号
    	$product_code='FAST_INSTANT_TRADE_PAY';
    	$total_amount=254.1;
    	$subject='测试订单'.$out_trade_no;


    	$request_param=[
    		'out_trade_no'  =>$out_trade_no,
    		'product_code'  =>$product_code,
    		'total_amount'  =>$total_amount,
    		'subject'       =>$subject
    	];


    	$param=[
    		'app_id'        =>$app_id,
    		'method'        =>$method,
    		'charset'       =>$charset,
    		'sign_type'     =>$sign_type,
    		'timestamp'     =>$timestamp,
    		'version'       =>$version,
            'notify_url'    =>$notify_url,
    		'return_url'    =>$return_url,
    		'biz_content'   =>json_encode($request_param)
    	];

        // 字典序排序
        ksort($param);
        // echo '<pre>';print_r($param);echo "</pre>";

        // 2 拼接 key1=value1&key2=value2...
        $str="";
        foreach ($param as $k => $v) {
            $str .=$k .'='. $v . '&';
        }
        // echo 'str:'.$str;die;
        $str=rtrim($str,'&');
        // 3 计算签名  网址：https://docs.open.alipay.com/291/106118
        $key=storage_path('keys/app_priv'); // 保存文件路径
        $priKey=file_get_contents($key);  // 把文件读入一个字符串
        $res=openssl_get_privatekey($priKey); // 判断私钥是否是可用的，可用返回资源id Resource id
        openssl_sign($str, $sign, $res,OPENSSL_ALGO_SHA256); // 对内容进行加密并输出乱码
        $sign=base64_encode($sign); // base64_encode是加密,而base64_decode是解密
        $param['sign']=$sign;

        // 4 urlencode 可将字符串以URL编码，用于编码处理
        $param_str='?';
        foreach ($param as $k => $v) {
            $param_str .= $k.'='.urlencode($v) . '&';
        }
        $param_str=rtrim($param_str,'&');
        $url=$ali_geteway . $param_str;
        // echo 12345;
    	// 发送GET请求
        // echo $url;die;
        header("Location:".$url); // 跳转页面

    }

}
