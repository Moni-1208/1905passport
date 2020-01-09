<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\UserModel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function get()
    {
    	echo date('Y-m-d H:i:s');

    	$data=[
    		'name'=>'小白',
    		'age'=>18,
    		'sex'=>1,
    	];

    	echo json_encode($data);

    }

    public function reg(Request $request)
    {
    	// print_r($request->input());
    	$pass1=$request->input('pass1');
    	$pass2=$request->input('pass2');
    	if($pass1 != $pass2){
    		die('两次输入密码不一致');
    	}
    	$password=password_hash($pass1, PASSWORD_DEFAULT);
    	$data=[
    		'name'		=> $request->input('name'),
    		'email'		=> $request->input('email'),
    		'mobile'	=> $request->input('mobile'),
    		'password' 	=> $password,
    		'last_login'=> time(),
    		'last_ip'	=>$_SERVER['REMOTE_ADDR'] // 获取远程ip
    	];
    	$res=UserModel::insertGetId($data);
    	dd($res);
    }

    public function login(Request $request)
    {
    	$name=$request->input('name');
    	$pass=$request->input('pass');
    	// echo 'pass'.$pass;die;
    	$u=UserModel::Where(['name'=>$name])->first();
    	// dd($u);die;
    	if($u){
    		// 验证密码
    		if(password_verify($pass, $u->password)){
    			// 登陆成功
    			echo "登陆成功";
    			$token=Str::random(32);
    			$response=[
    				'error'		=>0,
    				'msg'		=>'ok',
    				'data'		=>[
    					'token' =>$token
    				]
    			];
    		}else{
    			$response=[
    				'error'		=>4000003,
    				'msg'		=>'密码输入有误'
    			];
    		}
    	}else{
    		$response=[
    				'error'		=>4000004,
    				'msg'		=>'对不起，此用户不存在'
    			];
    	}
    	return $response;
    }

    /**
     * 获取用户列表 
     */
    public function UserList()
    {
    	print_r($_SERVER);die;
    	$user_token=$_SERVER['HTTP_POSTMAN_TOKEN'];
    	// echo "http_token".$user_token;echo '<hr>';
    	$current_url=$_SERVER['REQUEST_URI'];
    	// echo "当前url".$current_url;echo '<hr>';

    	$redis_key='str:count:url:'.md5($current_url);
    	// echo 'redis key:'.$redis_key;echo '<hr>';

    	// 取出计数 访问次数
    	$count=Redis::get($redis_key);
    	echo "接口访问的次数".$count;echo '<hr>';

    	// 判断最多访问次数
    	if($count>=10){
    		echo "访问次数已达到次数，请不要频繁刷新此接口，稍后再试。";
    		Redis::expire($redis_key,30);die;
    	}

    	// 计数 存入redis
    	$count=Redis::incr($redis_key);
    	// echo 'count:'.$count;
    }

}
		