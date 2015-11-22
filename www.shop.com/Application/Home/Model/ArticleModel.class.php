<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2015/11/14
 * Time: 23:07
 */

namespace Home\Model;


use Think\Model;

class ArticleModel extends Model
{
    public function getHelpArticle(){
        $helpArticles  = S('helpArticle');
        if(empty($helpArticles)){
        $this->field('a.id,a.name,a.article_category_id')->alias('a')->join("__ARTICLE_CATEGORY__ as ac on a.article_category_id=ac.id")->where(array('ac.is_help'=>1,'a.status'=>1));
        $helpArticles = $this->select();
            S('helpArticle',$helpArticles);
        }
        return $helpArticles;
    }

    public function getNews(){

        return $this->where(array('article_category_id'=>1,'status'=>1))->order('inputtime desc')->select();
    }
}