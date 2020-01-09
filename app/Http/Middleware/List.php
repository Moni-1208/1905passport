<?php

namespace App\Http\Middleware;

use Closure;

class List
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(isset($_SERVER['HTTP_POSTMEN_TOKEN']))
        {
            $redis_key='str:count:u:'.$_SERVER['HTTP_POSTMEN_TOKEN'].':url:'.$_SERVER['REQUEST_URI'];
            $count=Redis::get($redis_key);
            echo $count;
        }else{
            $response=[
                'error' => 400003,
                'msg'   =>"未授权"
            ];
            die(json_encode($response));
        }
        return $next($request);
    }
}
