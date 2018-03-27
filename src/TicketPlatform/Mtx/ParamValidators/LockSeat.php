<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

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
        'handlingfee',
        'pay_type',
        'recv_mobile_phone',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'handlingfee',
    ];
}