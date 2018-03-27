<?php

namespace biqu\TicketPlatform\Fhjy\ParamValidators;

use biqu\TicketPlatform\Fhjy\ParamValidators\Validator;

class GetSeatInfo extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'hall_no',
        'feature_app_no',
        'schedule_key',
        'area'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}