<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class CardBuyTicket extends Validator
{

    protected $app = [
        'password' ,
        'card_id',
        'type',
        'partner_id',
        'password',
        'trace_type_no',
        'old_price',
        'trace_price',
        'discount',
        'feature_no',
        'film_no',
        'ticket_num',
        'feature_app_no',
        'serial_num',
        'printpassword',
        'balance',
        'pay_type',
        'recv_mobile_phone',
        'send_type',
        'pay_result',
        'is_cmts_pay',
        'is_cmts_send_code',
        'pay_mobile',
        'book_sign',
        'payed',
        'send_mode_id',
        'order_no',
        'app_pric',
        'balance_pric',
        'modify_pric',
        'user_id',
        'is_card',
        'is_bts',
        'is_bts_member',
        'is_cut_out_member',
        'is_cut_out_non_member',
        'explain',
        'remarks',
        'third_pay_type'
    ];

    protected $nullable = [
        'partner_id',
        'pay_mobile',
        'send_mode_id',
        'is_cut_out_member',
        'is_cut_out_non_member',
    ];
}