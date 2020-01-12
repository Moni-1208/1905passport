<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
	

// 支付测试
Route::get('/test/alipay','Test\AlipayController@alipay');
// 同步回调
Route::get('/test/return','Test\AlipayController@aliReturn');
// 异步回调
Route::post('/test/notify','Test\AlipayController@notify');

// 接口测试 get
Route::get('Api/get','Api\TestController@get');
// 接口测试 注册
Route::post('Api/reg','Api\TestController@reg');
// 接口测试 登陆
Route::post('Api/login','Api\TestController@login');
// 接口测试 登陆
Route::post('Api/UserList','Api\TestController@UserList')->middleware('List');

// 接口测试 调用接口
Route::get('/goods','Test\GoodsController@goods');

// 凯撒加密
Route::get('/caesar','Test\CaesarController@caesar');

// CBC 解密
Route::get('/decrypt','Test\GoodsController@decrypt');

// 非对称解密
Route::get('/decrypt2','Test\GoodsController@decrypt2');


// 验签 注册 
Route::post('sign/reg','Check\SignController@reg');
// 登陆
Route::post('sign/login','Check\SignController@login');
// 获取用户信息接口
Route::get('sign/showTime','Check\SignController@showTime');