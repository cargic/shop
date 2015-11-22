<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/11
 * Time: 14:47
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Verify;

class VerifyController extends Controller
{

    public function index(){
        $config = array(
            'imageH'   => 50, // 验证码图片高度
            'imageW'   => 220, // 验证码图片宽度
            'length'   => 5, // 验证码位数
            'fontSize' => 20, // 验证码字体大小(px)
        );
        $verify = new Verify($config);
        $verify->entry();
    }
}