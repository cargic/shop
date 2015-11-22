<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2015/11/21
 * Time: 22:21
 */

namespace Home\Controller;


use Think\Controller;

class OrderInfoController extends Controller
{
    public function index(){
        if(!login()){
            $requestURL = $_SERVER['REQUEST_URI'];
            cookie('__LOGIN_RETURN_URL__',$requestURL);
            $this->error('您还未登录,请登录',U('Member/login'));
        }
        //>>1.准备所有的收货人信息
        $addressModel = D('Address');
        $addresses = $addressModel->getList();
        $this->assign('addresses',$addresses);
        //>>2.准备送货方式
        $deliveryModel = D('Delivery');
        $deliverys = $deliveryModel->getShowList();
        $this->assign('deliverys',$deliverys);
        //>>3.准备支付方式
        $payTypeModel = D('PayType');
        $payTypes = $payTypeModel->getShowList();
        $this->assign('payTypes',$payTypes);
        //>>4.准备购物车中的数据
        $shoppingCarModel= D('ShoppingCar');
        $shoppingCar = $shoppingCarModel->getList();
        $this->assign('shoppingCar',$shoppingCar);

        $this->display('index');
    }

    public function add(){
        //>>1.接收请求中的所有参数
        $params = I('post.');
        //>>2.让model保存
        $orderInfoModel = D('OrderInfo');
        $result = $orderInfoModel->add($params);
        if($result!==false){
            $this->success('下单成功!请求支付!',U('pay',array('id'=>$result)));
        }else{
            $this->error('添加失败!'.$orderInfoModel->getError());
        }
    }
    /**
     * 根据id查询出订单的编号和价格
     * @param $id
     */
    public function pay($id){
        $orderInfoModel = D('OrderInfo');
        $orderInfo  = $orderInfoModel->get($id);
        $this->assign($orderInfo);
        $this->display('pay');
    }
}