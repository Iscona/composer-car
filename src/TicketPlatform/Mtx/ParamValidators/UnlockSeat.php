<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

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