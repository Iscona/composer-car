<?php

namespace biqu\TicketPlatform\Fhjy\ParamValidators;

use biqu\TicketPlatform\Fhjy\ParamValidators\Validator;

class GetAllSchedule extends Validator
{
    protected $params;

    /**
     * 接口自定义的参数类型
     * @var [type]
     */
    protected $app = [
        'plan_date',
    ];

    /**
     * 可为空的字段
     * @var [type]
     */
    protected $nullable = [
        'plan_date',
    ];
}