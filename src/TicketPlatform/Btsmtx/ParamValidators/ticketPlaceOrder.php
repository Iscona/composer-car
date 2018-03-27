<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class TicketPlaceOrder extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
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
        // 'pay_seq_no',
        'user_id',
        'is_card',
        'is_bts',
        'is_bts_member',
        'is_cut_out_member',
        'is_cut_out_non_member',
        'third_pay_type',
        'modify_pric',
        'order_no',
        'app_pric',
        'balance_pric',
    ];

    protected $nullable = [
        'pay_mobile',
        'send_mode_id',
        'pay_seq_no',
        'is_cut_out_member',
        'is_cut_out_non_member',
    ];
}