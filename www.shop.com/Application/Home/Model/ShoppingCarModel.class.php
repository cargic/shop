<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2015/11/17
 * Time: 22:40
 */

namespace Home\Model;


use Think\Model;

class ShoppingCarModel extends Model
{
    /**
     * 将请求的数据添加到购物车中,如果未登录便保存到Cookie中,如果登录,则保存到数据库中.
     * @param mixed|string $requestData
     */
    public function add($requestData){
        if(!isLogin()){
            $this->addCookie($requestData);
        }else{
            $this->addDB($requestData);
        }
    }

    /**
     * 将请求中的数据保存到Cookie中
     * @param $item
     */
    private function addCookie($item){
        //>>1.判定cookie中是否有购物车数据,来确定是否为第一次购物,如果是第一次购物需要准备购物车的数组
        $shoppingCar = cookie('shopping_car');
        if(empty($shoppingCar)){
            $shoppingCar = array();
        }else{
            $shoppingCar = unserialize($shoppingCar);
        }
        //>>2.直接请求中的保存到购物车中
        //检查当前商品是否在购物车中
        $goods_ids = array_column($shoppingCar,'goods_id');
         if(in_array($item['goods_id'],$goods_ids)){
             //>>a. 如果在就更改商品的数量
                  //循环每一个明细从而找到明细内容,更改明细中的数量
             foreach($shoppingCar as &$shoppingCarItem){
                 if($shoppingCarItem['goods_id']==$item['goods_id']){
                     $shoppingCarItem['num'] = $shoppingCarItem['num']+$item['num'];
                     break;
                 }
             }
         }else{
             //>>b. 如果不在将本次购物明细添加到购物车中
             $shoppingCar[] = $item;
         }
        //>>3. 将购物车中的数据再次保存到cookie中,cookie中保存的是字符串,所以需要将数组序列化
        cookie('shopping_car',serialize($shoppingCar),86400);
    }

    /**
     * 将请求中的数据保存到数据库中
     */
    public function addDB($item){
        $item['member_id'] = UID;
        //先查询数据库中是否拥有该商品
        $count = $this->where(array('member_id'=>$item['member_id'],'goods_id'=>$item['goods_id']))->count();
        if($count>0){
            //如果有此商品就更新数量
            $this->where(array('member_id'=>$item['member_id'],'goods_id'=>$item['goods_id']));
            return parent::setInc('num',$item['num']);
        }else{
            //如果没有就新增此商品
            return parent::add($item);
        }
    }

    /**
     * 将cookie中的购物数据取出保存到数据库中
     */
    public function cookie2DB(){
        $shoppingCar = cookie('shopping_car');
        //>>1.如果cookie中没有任何内容, 直接返回
        if(empty($shoppingCar)){
            return;
        }
        //>>2.如果cookie中有购物明细, 需要将每个购物明细保存到数据库中
        $shoppingCar = unserialize($shoppingCar);
        foreach($shoppingCar as $item){
            $this->addDB($item);
        }
        //>>3.清空cookie中的数据
        cookie('shopping_car',null);
    }

    /**
     * 得到购物车的数据
     * 1.没有登录从Cookie中获取数据
     * 2.登录成功便从数据库中获取数据
     */
    public function getList(){
        if(!isLogin()){
            $shoppingCar = cookie('shopping_car');
            $shoppingCar = unserialize($shoppingCar);
            $this->buildShoppingCar($shoppingCar);//$shoppingCar需要重构
            return $shoppingCar;
        }else{
            $shoppingCar = $this->field('goods_id,num')->where(array('member_id'=>UID))->select();
        }
        //需要重构$shoppingCar中的数据
        $this->buildShoppingCar($shoppingCar);
        return $shoppingCar;
    }

    /**
     * 重构$shoppingCar
     */
    public function buildShoppingCar(&$shoppingCar){
        foreach($shoppingCar as &$item){
            $row = M('Goods')->field('name,logo,shop_price')->find($item['goods_id']);
            $item = array_merge($item,$row);
        }
    }
}