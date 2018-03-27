<?php

namespace biqu\TicketPlatform\Fhjy\ParamValidators;

use biqu\TicketPlatform\Fhjy\ParamValidators\Validator;

class CardScheduleDiscount extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'card_id',
        'password',
        'feature_no',
        'section_id',
        'schedule_key'
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}