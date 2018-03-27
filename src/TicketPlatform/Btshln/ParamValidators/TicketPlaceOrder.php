<?php

namespace biqu\TicketPlatform\Btshln\ParamValidators;

use biqu\TicketPlatform\Btshln\ParamValidators\Validator;

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
        'recv_mobile_phone',
        'seat_info',
        'ticket_price',
        'stand_price',
        'is_card',
        'user_id',
        'is_bts',
        'service_fee',
        'is_cut_out_member',
        'is_cut_out_non_member',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'is_cut_out_member',
        'is_cut_out_non_member',
    ];
}