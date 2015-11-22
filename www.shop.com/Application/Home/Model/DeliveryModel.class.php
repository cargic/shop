<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/20
 * Time: 15:29
 */

namespace Home\Model;


use Think\Model;

class DeliveryModel extends BaseModel
{
    public function get($id){
        return $this->field('id as delivery_id,name as delivery_name,price as delivery_price')->find($id);
    }

}