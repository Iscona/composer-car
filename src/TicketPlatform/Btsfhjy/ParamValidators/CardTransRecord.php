<?php

namespace biqu\TicketPlatform\Btsfhjy\ParamValidators;

use biqu\TicketPlatform\Btsfhjy\ParamValidators\Validator;

class CardTransRecord extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'card_id',
        'start_date',
        'end_date',
        'password',
        'is_bts',
        'is_bts_member',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [

    ];
}