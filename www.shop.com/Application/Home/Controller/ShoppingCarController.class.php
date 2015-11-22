<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2015/11/17
 * Time: 22:14
 */

namespace Home\Controller;


use Think\Controller;

class ShoppingCarController extends Controller
{
    public function index(){
        $shoppingCarModel = D('ShoppingCar');
        $shoppingCar = $shoppingCarModel->getList();
        $this->assign('shoppingCar',$shoppingCar);
        $this->display('index');
    }

    public function add(){
        $params = I('post.');//接受请求参数
        //将请求中的参数添加到购物车中
        $shoppingCarModel = D('ShoppingCar');
        $result = $shoppingCarModel->add($params);
        if($result!==false){
            $this->success('添加成功!',U('ShoppingCar/index'));
        }else{
            $this->error('添加失败!');
        }
    }
}