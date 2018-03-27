<?php

namespace biqu\TicketPlatform\Btsfhjy\ParamValidators;

use biqu\TicketPlatform\Btsfhjy\ParamValidators\Validator;

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
        'trace_no',
        'schedule_key',
        'feature_app_no',
        'is_discount',
        
        'user_id',
        'is_card',
        'is_bts',
        'is_bts_member',
        'is_cut_out_member',
        'is_cut_out_non_member',
        'explain',
        'remarks',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}