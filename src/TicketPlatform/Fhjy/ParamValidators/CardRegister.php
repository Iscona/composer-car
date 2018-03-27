<?php

namespace biqu\TicketPlatform\Fhjy\ParamValidators;

use biqu\TicketPlatform\Fhjy\ParamValidators\Validator;

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
        'member_fee',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}