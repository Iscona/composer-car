<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class PrintTicket extends Validator
{

    protected $app = [
        'order_no',
        'valid_code',
        'request_type'
    ];
}