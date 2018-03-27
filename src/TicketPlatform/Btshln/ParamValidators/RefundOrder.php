<?php

namespace biqu\TicketPlatform\Btshln\ParamValidators;

use biqu\TicketPlatform\Btshln\ParamValidators\Validator;

class RefundOrder extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'pay_type',
        'password',
        'card_id',
        'app_seat_no',
        'order_no',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'password',
        'card_id',
    ];
}