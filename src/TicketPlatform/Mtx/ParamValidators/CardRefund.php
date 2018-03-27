<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class CardRefund extends Validator
{

    protected $app = [
        'password',
        'card_id',
        'trace_type',
        'trace_no',
        'trace_price',
        'price',
        'type',
        'trace_memo'
    ];

    protected $nullable = [];
}