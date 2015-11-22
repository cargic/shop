<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/20
 * Time: 10:42
 */

namespace Home\Model;


use Think\Model;

class RegionModel extends Model
{

    /**
     * 根据父地区查询 子地区
     * @param int $parent_id
     */
    public function getChildren($parent_id = 0){
        return $this->field('id,name')->where(array('parent_id'=>$parent_id))->select();
    }
}