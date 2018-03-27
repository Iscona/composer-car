<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

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
        'remarks',
        'explain',
        'is_bts',
        'is_bts_member',  
    ];

    protected $nullable = ['trace_no'];
}