<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class TicketPlaceOrder extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'serial_num',
        'app_seat_no',
        'seat_info',
        'ticket_price',
        'service_fee',
        'feature_app_no',
        'user_id',
        'is_card',
        'is_bts',
    ];

    protected $nullable = [
    ];
}