<?php

namespace biqu\TicketPlatform\Dx\ParamValidators;

use biqu\TicketPlatform\Dx\ParamValidators\Validator;

class UnlockSeat extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'feature_app_no',
        'seat_infos',
        'order_no'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [];
}