<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function _initialize(){
        //>>1.准备商品分类数据
        $goodsCategoryModel = D('GoodsCategory');
        $goodsCategorys = $goodsCategoryModel->getList();
        $this->assign('goodsCategorys', $goodsCategorys);

        //>>2.准备所有的帮助的分类
        $articleCategoryModel = D('ArticleCategory');
        $helpArticleCategorys = $articleCategoryModel->getHelpArticleCategory();
        $this->assign('helpArticleCategorys',$helpArticleCategorys);

        //>>3.准备所有帮助类文章的数据
        $articleModel = D('Article');
        $articles =  $articleModel->getHelpArticle();
        $this->assign('articles',$articles);
    }


    public function index(){
        //>>1.准备不同状态的商品信息, 分别准备5个,分发到页面相应位置
        $goodsModel = D('Goods');
        //>>1.1准备疯狂抢购的商品
        $goods_2s = $goodsModel->getGoodsbyGoodsStatus(2);

        //>>1.2准备热卖的商品
        $goods_4s = $goodsModel->getGoodsbyGoodsStatus(4);

        //>>1.3准备推荐的商品
        $goods_1s = $goodsModel->getGoodsbyGoodsStatus(1);

        //>>1.4准备新品上架的商品
        $goods_8s = $goodsModel->getGoodsbyGoodsStatus(8);

        //>>1.6准备猜你喜欢的商品
        $goods_16s = $goodsModel->getGoodsbyGoodsStatus(16);

        //>>2.为页面准备网页快报文章
        $webnews = D('Article');
        $newss = $webnews->getNews();

        $this->assign(array(
            'meta_title'=>'京西商城首页',
            'goods_2s'=>$goods_2s,
            'goods_4s'=>$goods_4s,
            'goods_1s'=>$goods_1s,
            'goods_8s'=>$goods_8s,
            'goods_16s'=>$goods_16s,
            'newss'=>$newss,
        ));
        $this->display('index');
    }

    public function lst()
    {
        $this->assign('isHiddenMenu','none');
        $this->assign('meta_title','商品列表');
        $this->display('lst');
    }

    public function show($id){
        $goodsModel = D('Goods');
        $goods = $goodsModel->get($id);
        $this->assign($goods);

        $this->assign('isHiddenMenu','none');
        $this->assign('meta_title','['.$goods['name']."]--京东商城");
        $this->display('show');
    }

}