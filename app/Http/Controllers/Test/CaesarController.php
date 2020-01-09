<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CaesarController extends Controller
{
    public function caesar()
    {
    	$char='hello word';
    	$length=strlen($char);
    	echo $length;echo "<hr>";
    	for($i=0;$i<$length;$i++)
    	{
    		echo $char[$i].'>>>'.ord($char[$i]);echo '</br>';
    		$ord=ord($char[$i])+3;
    		$chr=chr($ord);
    		echo $char[$i].'>>>'.$ord.'>>>'.$chr;echo '<hr>';
    		$pass.=$chr;
    	}
    	echo '</br>';
    	echo $pass;

    	// 解密
    }
}
