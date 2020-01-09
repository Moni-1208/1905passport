<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function goods()
    {
    	print_r($_GET);
    	echo "当前查询id为".$_GET['id'];	
    }

    // CBC 解密
    public function decrypt()
    {
    	// 接收加密的数据
    	$data=base64_decode($_GET['data']);
        echo "接收到的密文：".$data;echo "<br>";

    	$method='AES-256-CBC';
    	$key='1905api';
    	$iv='qwertyuiop123456';

    	// 解密
    	$dec_data=openssl_decrypt($data, $method, $key,OPENSSL_RAW_DATA,$iv);
    	echo "解密数据为：".$dec_data;

        echo "<hr>";
        $pos=json_decode($dec_data  );
        print_r($pos);

    }

    // 非对称解密
    public function decrypt2()
    {
        // 接收加密的数据
        $enc_data_str=$_GET['data'];
        echo "接受的base64密文：".$enc_data_str;
        echo "<hr>";
        $base64_decode_str=base64_decode($enc_data_str);
        echo "解密base64的密文：".$base64_decode_str;
        echo "<hr>";
        // 使用公钥解密
        $pub_key=file_get_contents(storage_path('keys/pub.key'));
        openssl_public_decrypt($base64_decode_str, $dec_data, $pub_key);
        echo "解密数据：".$dec_data;
    }
}

