<?php
return array(
	//'配置项'=>'配置值'
    'DB_TYPE'                => 'mysql', // 数据库类型
    'DB_HOST'                => '127.0.0.1', // 服务器地址
    'DB_NAME'                => 'shop', // 数据库名
    'DB_USER'                => 'root', // 用户名
    'DB_PWD'                 => 'MAXIKUN', // 密码
    'DB_PORT'                => '3306', // 端口
    'DB_PREFIX'              => '', // 数据库表前缀

    /**
     * 使用redis作为缓存的软件
     */
    'DATA_CACHE_TYPE'        => 'Redis', // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'REDIS_HOST'             => '127.0.0.1',
    'REDIS_PORT'             => 6379,
    'DATA_CACHE_PREFIX'      => 'mxker_',

    'MAIL_CONFIG'            =>array(
        'Host' => 'smtp.163.com',                    // 设置邮件的服务器
        'Username' => 'mxk_crazy@163.com',              // 登陆用户的用户名
        'Password' => 'MAXIKUN5120',
        'From' => 'mxk_crazy@163.com',
    ),
    'ALIPAY_CONFIG'         =>array(
        'partner' => '2088802175815304',  //合作身份者id，以2088开头的16位纯数字
        'seller_email' => '517421372@qq.com',//收款支付宝账号，一般情况下收款账号就是签约账号
        'key' => '35uusk2ffqh69jsa8uqgfvdk5mod9afb',//安全检验码，以数字和字母组成的32位字符
        'sign_type'=>strtoupper('MD5'),//签名方式 不需修改
        'input_charset'=>strtolower('utf-8'),//字符编码格式 目前支持 gbk 或 utf-8
        'cacert'=>getcwd().'\\cacert.pem',//请保证cacert.pem文件在当前文件夹目录中
        'transport'=>'http'//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    )
);