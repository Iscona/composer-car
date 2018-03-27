<?php

namespace biqu\TicketPlatform\Fhjy\ParamValidators;

use biqu\TicketPlatform\Fhjy\ParamValidators\Validator;

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
        'schedule_key'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}