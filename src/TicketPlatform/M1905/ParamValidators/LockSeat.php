<?php

namespace biqu\TicketPlatform\M1905\ParamValidators;

use biqu\TicketPlatform\M1905\ParamValidators\Validator;

class LockSeat extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'feature_app_no',
        'serial_num',
        'seat_infos',
        'ticket_price',
        'handlingfee'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'handlingfee'
    ];
}