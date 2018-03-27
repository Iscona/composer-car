<?php

namespace biqu\TicketPlatform\Btsfhjy\ParamValidators;

use biqu\TicketPlatform\Btsfhjy\ParamValidators\Validator;

class RefundOrder extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'order_no',
        'card_id',
        'price',
        'password',
        'explain',
        'remarks',
        'pay_type',
        'is_bts',
        'is_bts_member',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'card_id',
        'password'
    ];
}