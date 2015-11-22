<?php
defined('WEB_URL') or define('WEB_URL','http://www.shop.com');
return array(
    'TMPL_PARSE_STRING'  =>array(
        '__CSS__' => WEB_URL.'/Public/Home/css', // 更改默认的/Public 替换规则
        '__JS__'     => WEB_URL.'/Public/Home/js', // 增加新的JS类库路径替换规则
        '__IMG__' => WEB_URL.'/Public/Home/images', // 增加新的上传路径替换规则
        '__IMGS__' =>'http://admin.shop.com/Uploads', // 增加新的上传路径替换规则
        '__BRAND__' => "http://itsource-brand.b0.upaiyun.com", // brand又拍云空间中的地址
        '__GOODS__' => "http://itsource-goods.b0.upaiyun.com", // goods又拍云空间中的地址

    )
);