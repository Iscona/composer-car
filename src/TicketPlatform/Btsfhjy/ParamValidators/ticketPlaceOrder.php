<?php

namespace biqu\TicketPlatform\Btsfhjy\ParamValidators;

use biqu\TicketPlatform\Btsfhjy\ParamValidators\Validator;

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
        'pay_type',
        'recv_mobile_phone',
        'seat_info',
        'ticket_price',
        'handlingfee',
        'schedule_key',
        'platform_trace_no',
        'trace_no',
        'user_id',
        'is_bts',
        'is_bts_member',
        'is_cut_out_member',
        'is_cut_out_non_member',        
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}