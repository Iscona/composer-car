<?php

namespace biqu\TicketPlatform\Hln\ParamValidators;

use biqu\TicketPlatform\Hln\ParamValidators\Validator;

class GetOrderStatus extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'serial_num',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}