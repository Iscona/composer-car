<?php

namespace biqu\TicketPlatform\Hln\ParamValidators;

use biqu\TicketPlatform\Hln\ParamValidators\Validator;

class CardBuyTicket extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'serial_num',
        'password',
        'card_id',
        'seat_info',
        'ticket_price',
        'recv_mobile_phone',
        'pay_price',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}