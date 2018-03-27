<?php

namespace biqu\TicketPlatform\Dx\ParamValidators;

use biqu\TicketPlatform\Dx\ParamValidators\Validator;

class TicketPlaceOrder extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'seat_info',
        'serial_num',
        'trace_no',
        'recv_mobile_phone',
        'ticket_price',
        'handlingfee',
        'feature_app_no',
        'update_time'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [];
}