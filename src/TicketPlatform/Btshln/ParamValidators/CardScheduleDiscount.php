<?php

namespace biqu\TicketPlatform\Btshln\ParamValidators;

use biqu\TicketPlatform\Btshln\ParamValidators\Validator;

class CardScheduleDiscount extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'card_id',
        'serial_num',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}