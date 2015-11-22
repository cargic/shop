<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2015/11/14
 * Time: 22:33
 */

namespace Home\Model;


use Think\Model;

class ArticleCategoryModel extends Model
{
    public function getHelpArticleCategory(){
        $helpArticleCategorys = S('helpArticleCategorys');
        if(empty($helpArticleCategorys)){
            $helpArticleCategorys = $this->field('id,name')->where(array('is_help'=>1,'status'=>1))->select();
            S('helpArticleCategorys',$helpArticleCategorys);
        }
        return $helpArticleCategorys;
    }
}