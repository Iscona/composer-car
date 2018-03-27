<?php

namespace biqu\TicketPlatform\Btshln\ParamValidators;

use biqu\TicketPlatform\Btshln\ParamValidators\Validator;

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
        'is_bts',
        'is_bts_member',
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