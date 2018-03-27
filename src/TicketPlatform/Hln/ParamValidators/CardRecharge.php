<?php

namespace biqu\TicketPlatform\Hln\ParamValidators;

use biqu\TicketPlatform\Hln\ParamValidators\Validator;

class CardRecharge extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'card_id',
        'price',
        'serial_num',
        'plat_no',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}