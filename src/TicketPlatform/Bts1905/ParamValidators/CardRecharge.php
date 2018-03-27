<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class CardRecharge extends Validator
{
    protected $app = [
        'card_id',
        'price',            //充值金额
        'serial_num',       //充值订单号（网售商生成，不得重复，防止重复提交多次充值）
    ];

    protected $nullable = ['serial_num'];
}