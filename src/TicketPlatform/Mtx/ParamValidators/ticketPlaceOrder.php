<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

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
        'order_no',
        'app_pric',
        'balance_pric',
        'modify_pric',
        // 'pay_seq_no',
    ];

    protected $nullable = [
        'pay_mobile',
        'send_mode_id',
        'pay_seq_no',
    ];
}