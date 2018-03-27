<?php

namespace biqu\TicketPlatform\Btshln\ParamValidators;

use biqu\TicketPlatform\Btshln\ParamValidators\Validator;

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
        'is_bts',
        'is_bts_member',
        'explain',
        'remarks',
        'password',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'serial_num',
        'plat_no',
    ];
}