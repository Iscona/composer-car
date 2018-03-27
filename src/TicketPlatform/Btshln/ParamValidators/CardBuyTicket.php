<?php

namespace biqu\TicketPlatform\Btshln\ParamValidators;

use biqu\TicketPlatform\Btshln\ParamValidators\Validator;

class CardBuyTicket extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'serial_num',
        'app_seat_no',
        'password',
        'card_id',
        'seat_info',
        'ticket_price',
        'recv_mobile_phone',
        'pay_price',
        'is_bts',
        'is_bts_member',
        'is_bts_ticket',
        'user_id',
        'is_card',
        'explain',
        'remarks',
        'service_fee',
        'app_price',
        'is_cut_out_member',
        'is_cut_out_non_member'
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