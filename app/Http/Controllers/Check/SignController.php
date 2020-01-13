<?php

namespace App\Http\Controllers\Check;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\SignModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class SignController extends Controller
{
    // 注册接口
    public function reg()
    {
    	$data=$_POST;
        // dd($data);die;
    	if($data['pwds']!=$data['s_pwd']){
    		$response=[
    			'error'=>'1020',
    			'msg'=>'两次密码不一样'
    		];
    		return $response;
    	}
    	// 移除确认密码
    	unset($data['pwds']);
        // print_r($data);die;
        // 密码加密
        $data['s_pwd']=password_hash($data['s_pwd'], PASSWORD_BCRYPT);
        // 验证用户是否已存在
        $user=SignModel::where(['s_name'=>$data['s_name']])->first();
        if($user){
            $response = [
                'errno' => 500003,
                'msg'   => "用户名已被使用"
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        // 验证tel是否已存在
        $user=SignModel::where(['s_tel'=>$data['s_tel']])->first(); 
        if($user){
             $response = [
                'errno' => 500002,
                'msg'   => "tel已被使用"
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        // 验证email是否已存在
        $user=SignModel::where(['s_email'=>$data['s_email']])->first();
        if($user){
            $response = [
                'errno' => 500003,
                'msg'   => "Email已被使用"
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        // echo __METHOD__;die;
        $uid = SignModel::insertGetId($data);
        if($uid)
        {
            $response = [
                'errno' => 0,
                'msg'   => 'ok'
            ];
        }else{
            $response = [
                'errno' => 500001,
                'msg'   => "服务器内部错误,请稍后再试"
            ];
        }
        return json_encode($response);
    }



    //登录
    public function login(Request $request){
        // print_r($_POST);
        $value=$request->input('s_name');
        $s_pwd=$request->input('s_pwd');
        // 按name找记录
        $user1=SignModel::where(['s_name'=>$value])->first();
        $user2=SignModel::where(['s_email'=>$value])->first();
        $user3=SignModel::where(['s_tel'=>$value])->first();
        // print_r($user->toArray());
        if($user1==NULL && $user2==NULL && $user3==NULL){
            $response = [
                'errno' => 400004,
                'msg'   => "用户不存在"
            ];
            return $response;
        }
        if($user1)     // 使用用户名登录
        {
            if(password_verify($s_pwd,$user1->s_pwd)){
                $s_id = $user1->s_id;
                $response = [
                    'errno' => 'ok',
                    'msg'   => '登陆成功'
                ];
            }else{
                $response = [
                    'errno' => 400003,
                    'msg'   => 'password wrong'
                ];
                return $response;
            }
        }
        if($user2){        //使用 email 登录
            if(password_verify($s_pwd,$user2->s_pwd)){
                echo "ok";
                $s_id = $user2->s_id;
            }else{
                $response = [
                    'errno' => 400003,
                    'msg'   => 'password wrong'
                ];
                return $response;
            }
        }
        if($user3){        // 使用电话号登录
            if(password_verify($s_pwd,$user3->s_pwd)){
                echo "ok";
                $s_id = $user3->s_id;
            }else{
                $response = [
                    'errno' => 400003,
                    'msg'   => 'password wrong'
                ];
                return $response;
            }
        }
        // 生成token
        $token=$this->getToken($s_id);
        $redis_token_key='str:user:token:'.$s_id;
        echo $redis_token_key;
        Redis::set($redis_token_key,$token,86400); // 生成token 设置存储时间

        $response=[
            'error'=>0,
            'msg'=>'ok',
            'data'=>[
                's_id'=>$s_id,
                'token'=>$token
            ]
        ];
        return $response; // baixue  
    }
        // // 按email找记录
        // $user=SignModel::where(['s_email'=>$value])->first();
        // // print_r($user->toArray());
        // if ($user) {
        //     // print_r($user->toArray());
        //     $s_pwd=$user->s_pwd;
        //     // echo "密码：".$s_pwd;
        //     if (password_verify($s_pwd, $user->s_pwd)) {
        //         $reponse=[
        //             'error'=>'ok',
        //             'msg'=>'密码正确'
        //         ];
        //         return $reponse;
        //     }else{
        //         $reponse=[
        //             'error'=>500004,
        //             'msg'=>'密码错误'
        //         ];
        //         return $reponse;
        //     }
        // }
        // // 按tel找记录
        // $user=SignModel::where(['s_tel'=>$value])->first();
        // // print_r($user->toArray());
        // if ($user) {
        //     // print_r($user->toArray());
        //     $s_pwd=$user->s_pwd;
        //     // echo "密码：".$s_pwd;
        //     if (password_verify($s_pwd, $user->s_pwd)) {
        //         $reponse=[
        //             'error'=>'ok',
        //             'msg'=>'密码正确'
        //         ];
        //         return $reponse;
        //     }else{
        //         $reponse=[
        //             'error'=>500004,
        //             'msg'=>'密码错误'
        //         ];
        //         return $reponse;
        //     }
        // }else{
        //     $reponse=[
        //             'error'=>500004,
        //             'msg'=>'用户不存在'
        //         ];
        //         return $reponse;
        // }

        // 生成用户token
        public function getToken($s_id)
        {
            $token=md5(time().mt_rand(1111,9999).$s_id);
            return substr($token, 5,20);
        }

        // 获取用户信息接口
        public function showTime()
        {
            if(empty($_SERVER['HTTP_TOKEN']) || empty($_SERVER['HTTP_UID']))
            {
                $response = [
                    'errno' => 40003,
                    'msg'   => 'A授权失败!'
                ];
                return $response;
            }
            // print_r($_SERVER);
            $token=$_SERVER['HTTP_TOKEN'];
            $uid=$_SERVER['HTTP_UID'];
            echo 'token:'.$token;echo "</br>";
            // echo 'uid:'.$uid;echo "</br>";
            // 拼接值
            $redis_token_key='str:user:token:'.$uid;
            // echo 'redis_token_key:'.$redis_token_key;echo "</br>";
            // 验证token是否有效
            $cache_token=Redis::get($redis_token_key);
            echo 'cache_token:'.$cache_token;echo "</br>";
            if($token==$cache_token)        // token 有效
            {
                $data = date("Y-m-d H:i:s");
                $response = [
                    'errno' => 0,
                    'msg'   => 'ok',
                    'data'  => $data
                ];
            }else{
                $response = [
                    'errno' => 40003,
                    'msg'   => 'Token Not Valid!'
                ];
            }
            return $response;
        }
    

}
