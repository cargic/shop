<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2015/11/14
 * Time: 23:45
 */

namespace Home\Model;


use Think\Model;

class GoodsModel extends Model
{
    public function getGoodsbyGoodsStatus($goods_status,$num =5){
        $wheres = array('status'=>1,'is_on_sale'=>1);
        $rows = $this->field('id,logo,name,shop_price')->where($wheres)->
        where("goods_status&{$goods_status}>0")->limit($num)->order('sort')->select();
        return $rows;
    }

    public function get($id){
//        1.根据id查询出商品的基本信息和品牌信息
        $goods = $this->field('g.*,b.name as brand_name')->alias('g')->
        join('__BRAND__ as b  on g.brand_id = b.id')->where(array('g.id'=>$id))->find();

        //>>2.1查询出当前商品的分类以及父及分类的数据
        $sql = "select gc.id,gc.name from goods_category as gc2 ,goods_category as gc
where  gc.lft<=gc2.lft and gc.rght >= gc2.rght  and gc2.id ={$goods['goods_category_id']}
and gc.status=1  order by gc.lft";
        $goodsCategorys  = $this->query($sql);
        $goods['goodsCategorys'] = $goodsCategorys;

        //>>3.获取当前商品的相册数据
        //>>3.1 查询出path的值
        $gallerys =  M('GoodsGallery')->field('path')->where(array('goods_id'=>$id))->select();
        //>>3.2 单独取出path的值
        $gallerys = array_column($gallerys,'path');
        array_unshift($gallerys,$goods['logo']);
        $goods['gallerys'] = $gallerys;

        return $goods;
    }
}