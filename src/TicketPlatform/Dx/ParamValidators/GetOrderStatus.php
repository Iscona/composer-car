<?php

namespace biqu\TicketPlatform\Dx\ParamValidators;

use biqu\TicketPlatform\Dx\ParamValidators\Validator;

class GetOrderStatus extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'serial_num',
        'card_id'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'card_id'
    ];
}