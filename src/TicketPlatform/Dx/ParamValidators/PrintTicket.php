<?php

namespace biqu\TicketPlatform\Dx\ParamValidators;

use biqu\TicketPlatform\Dx\ParamValidators\Validator;

class PrintTicket extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'valid_code',
        'card_id',
        'request_type'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'card_id',
    ];
}