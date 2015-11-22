<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/20
 * Time: 10:50
 */

namespace Home\Controller;


use Think\Controller;

class RegionController extends Controller
{

    public function getChildren($parent_id = 0){
        $regionModel = D('Region');
        $rows = $regionModel->getChildren($parent_id);
        $this->ajaxReturn($rows);
    }
}