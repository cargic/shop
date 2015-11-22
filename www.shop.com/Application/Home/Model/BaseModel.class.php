<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/20
 * Time: 15:41
 */

namespace Home\Model;


use Think\Model;

class BaseModel extends Model
{
    public function getShowList(){
        return $this->where(array('status'=>1))->select();
    }
}