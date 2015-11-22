<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/20
 * Time: 17:08
 */

namespace Home\Model;


use Think\Model;

class OrderInfoModel extends Model
{

    /**
     * 将请求中的订单数据保存到三个表中
     * order_info
     * order_info_item
     * invoice:发票表
     * @param mixed|string $requestData
     */
    public function add($requestData){
        $this->startTrans();
        //>>1.准备order_info表中的数据
        $orderinfo = array();
        //>>1.1根据收货人地址的id查询出需要的数据
        $address_id = $requestData['address_id'];
        $addressModel = D('Address');
        $address = $addressModel->get($address_id);
        $orderinfo =  array_merge($orderinfo,$address);
        //>>1.2根据收货方式的id查询出所需要的数据
        $delivery_id = $requestData['delivery_id'];
        $deliveryModel = D('Delivery');
        $delivery = $deliveryModel->get($delivery_id);
        $orderinfo =  array_merge($orderinfo,$delivery);
        //>>1.3.根据支付方式的id查询出支付方式的名字
        $orderinfo['pay_type_id'] = $requestData['pay_type_id'];
        $orderinfo['pay_type_name'] = M('PayType')->getFieldById($requestData['pay_type_id'],'name');
        //>>1.4.准备会员id和录入时间
        $orderinfo['member_id'] = UID;
        $orderinfo['inputtime'] = NOW_TIME;
        //>>1.5 准备下单时的订单状态
        $orderinfo['order_status'] = 0;
        $orderinfo['shipping_status'] = 0;
        $orderinfo['pay_status'] = 0;
        //>>1.6 准备订单的价格
        //>>a. 根据购物车中明细的价格,计算出总价格
        $shoppingCarModel = D('ShoppingCar');
        $shoppingCar = $shoppingCarModel->getList();
        $price = 0;
        foreach($shoppingCar as $item){
            $price+= $item['shop_price'] * $item['num'];
        }
        //>>b.将明细的价格总和  与 运费相加
        $orderinfo['price'] = $price+$orderinfo['delivery_price'];  //明细的价格总和 与运费的价格
        //>>2.将数据添加到orderinfo表中
        $order_info_id = parent::add($orderinfo);
        if($order_info_id===false){
            $this->error = '保存失败!';
            $this->rollback();
            return false;
        }

        //>>3.准备order_info_item表中的数据
        $orderInfoitems = array();
        foreach($shoppingCar as $item){
            $item['order_info_id'] = $order_info_id;
            $item['price'] = $item['num']*$item['shop_price'];
            $orderInfoitems[] = $item;
        }

        //>>4.将order_info_item表需要的数据保存
        $orderInfoItemModel = M('OrderInfoItem');
        $result = $orderInfoItemModel->addAll($orderInfoitems);
        if($result===false){
            $this->error = '保存明细失败!';
            $this->rollback();
            return false;
        }

        //>>5.准备invoice表中的数据
        //>>5.1 准备发票的名字
        $invoice_name = '';
        if($requestData['invoice_type']==0){  //个人
            $userinfo = login();
            $invoice_name = $userinfo['username'];
        }else{  //单位, 从请求中得到发票的名字
            $invoice_name = $requestData['invoice_name'];
        }
        //>>5.2 准备发票的内容
        $invoice_content = '';
        if($requestData['invoce_content']=='明细'){
            foreach($orderInfoitems as $orderInfoitem){
                $invoice_content.= ($orderInfoitem['name'] .'&nbsp;'. $orderInfoitem['num'] .'&nbsp;'. $orderInfoitem['shop_price'].'<br/>');
            }
        }else{
            $invoice_content = $requestData['invoce_content'];
        }

        $invoice = array();
        $invoice['order_info_id'] = $order_info_id;
        $invoice['name'] = $invoice_name;
        $invoice['content'] = $invoice_content;
        $invoice['price'] = $price;

        //>>6.将invoice表需要的数据保存
        $sn = date('Ymd').str_pad($order_info_id,12,0,STR_PAD_LEFT);
        $result  = parent::save(array('id'=>$order_info_id,'sn'=>$sn,'invoice_id'=>$invoice_id));
        if($result===false){
            $this->error = '更新订单失败!';
            $this->rollback();
            return false;
        }
        $this->commit();
        return $order_info_id;
    }

    public function get($id){
        return $this->field('id,sn,price,pay_type_id')->find($id);
    }

    public function doPay($id){
        //>>1.根据id找到订单信息
        $orderInfo = $this->find($id);
        //>>2.根据订单信息进行支付
        $alipay_config = C('ALIPAY_CONFIG');
        vendor('Alipay.lib.alipay_submit#class');
        /**************************请求参数**************************/

        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = "http://www.shop.com/index.php/OrderInfo/notify_url";
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = "http://www.shop.com/index.php/OrderInfo/return_url";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商户订单号
        $out_trade_no = $orderInfo['sn'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = '京西商城订单';
        //必填

        //付款金额
        $price = $orderInfo['price'];
        //必填

        //商品数量
        $quantity = "1";
        //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
        //物流费用
        $logistics_fee = $orderInfo['delivery_price'];
        //必填，即运费
        //物流类型
        $logistics_type = "EXPRESS";
        //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        //物流支付方式
        $logistics_payment = "SELLER_PAY";
        //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        //订单描述

        $body = '订单描述订单描述订单描述订单描述订单描述订单描述订单描述订单描述';
        //商品展示地址
        $show_url = "http://www.shop.com/index.php/OrderInfo/show/id/".$id;
        //需以http://开头的完整路径，如：http://www.商户网站.com/myorder.html

        //收货人姓名
        $receive_name = $orderInfo['receiver'];
        //如：张三

        //收货人地址
        $receive_address = $orderInfo['province_name'].$orderInfo['city_name'].$orderInfo['area_name'].$orderInfo['detail_address'];
        //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号

        //收货人邮编
        $receive_zip = '123456';
        //如：123456

        //收货人电话号码
        $receive_phone = $orderInfo['tel'];
        //如：0571-88158090

        //收货人手机号码
        $receive_mobile = $orderInfo['tel'];
        //如：13312341234


        /************************************************************/

//构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_partner_trade_by_buyer",
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "payment_type"	=> $payment_type,
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url,
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "price"	=> $price,
            "quantity"	=> $quantity,
            "logistics_fee"	=> $logistics_fee,
            "logistics_type"	=> $logistics_type,
            "logistics_payment"	=> $logistics_payment,
            "body"	=> $body,
            "show_url"	=> $show_url,
            "receive_name"	=> $receive_name,
            "receive_address"	=> $receive_address,
            "receive_zip"	=> $receive_zip,
            "receive_phone"	=> $receive_phone,
            "receive_mobile"	=> $receive_mobile,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );

        header('Content-Type: text/html;charset=utf-8');
        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }
}