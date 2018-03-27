<?php

namespace biqu\TicketPlatform\Hln\ParamValidators;

use biqu\TicketPlatform\Hln\ParamValidators\Validator;

class CardRegister extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'password',
        'mobile_phone',
        'id_num',
        'member_name',
        'balance',
        'member_type_no',
        'trace_no',
        'card_cost_fee',
        'annual_fee',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'member_name',
        'annual_fee',
        'card_cost_fee',
        'balance',
    ];
}