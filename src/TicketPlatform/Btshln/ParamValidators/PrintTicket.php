<?php

namespace biqu\TicketPlatform\Btshln\ParamValidators;

use biqu\TicketPlatform\Btshln\ParamValidators\Validator;

class PrintTicket extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'valid_code',
        'order_no',
        'request_type',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}