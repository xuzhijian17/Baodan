<?php
return [
    'adminEmail' => 'admin@example.com',

    // 定义响应信息
    'codeinfo' => [
    	'0' => '成功',
    	'1' => '参数验证错误',
    	'2' => '用户名错误',
    	'3' => '验证码错误',
    	'4' => '用户注册失败',
    	'101' => '接单失败',
    	'102' => '拒单失败',
    	'103' => '结单失败',
    	'104' => '撤销失败',
    	'401' => '未授权',
    	'402' => '获取token失败',
    	'403' => '无效token',
    	'404' => 'token过期'
    ],

    // token params
    'token' => [
    	'exp' => time()+60*60*24,
    	'secret' => '9e62c4fffcfdbe106ce9566b62d863d5'
    ]
];
