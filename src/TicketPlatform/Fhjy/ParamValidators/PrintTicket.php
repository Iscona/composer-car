<?php

namespace biqu\TicketPlatform\Fhjy\ParamValidators;

use biqu\TicketPlatform\Fhjy\ParamValidators\Validator;

class PrintTicket extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'order_no',
        'valid_code',
        'request_type',
        'app_seat_no'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}