<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class CardRecharge extends Validator
{
    protected $app = [
        'card_id',
        'password',
        'type',
        'price',
        'trace_memo',
        'partner_id'
    ];

    protected $nullable = ['partner_id', 'trace_memo'];
}