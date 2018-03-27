<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class PrintTicket extends Validator
{

    protected $app = [
        'order_no',
        'valid_code',
        'request_type'
    ];
}