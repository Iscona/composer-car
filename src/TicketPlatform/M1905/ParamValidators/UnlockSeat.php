<?php

namespace biqu\TicketPlatform\M1905\ParamValidators;

use biqu\TicketPlatform\M1905\ParamValidators\Validator;

class UnlockSeat extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'order_no',
    ];
}