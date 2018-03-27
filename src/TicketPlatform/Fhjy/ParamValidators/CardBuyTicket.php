<?php

namespace biqu\TicketPlatform\Fhjy\ParamValidators;

use biqu\TicketPlatform\Fhjy\ParamValidators\Validator;

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
        'seat_info',
        'ticket_price',
        'handlingfee',
        'recv_mobile_phone',
        'serial_num',
        'schedule_key',
        'feature_app_no',
        'is_discount',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}