<?php

namespace biqu\TicketPlatform\Dx\ParamValidators;

use biqu\TicketPlatform\Dx\ParamValidators\Validator;

class CardBuyTicket extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'card_id',
        'password',
        'feature_app_no',
        'serial_num',
        'seat_info',
        'ticket_price',
        'handlingfee',
        'is_discount',
        'ticket_type',
        'update_time',
        'trace_no'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}