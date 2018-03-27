<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class LockSeat extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'feature_app_no',
        'serial_num',
        'seat_infos',
        'ticket_price',
        'service_fee',
        'start_time',
        'bts_timestart_time',
        'film_name',
        'notice_sms_key',
        'notice_sms_secret',
        'notice_sms_continuous_lock_seat_fail_tpl_id',
        'cellphone',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'handlingfee'
    ];
}