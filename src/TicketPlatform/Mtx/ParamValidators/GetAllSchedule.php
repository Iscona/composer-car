<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

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