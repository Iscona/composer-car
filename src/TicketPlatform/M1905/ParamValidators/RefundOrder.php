<?php

namespace biqu\TicketPlatform\M1905\ParamValidators;

use biqu\TicketPlatform\M1905\ParamValidators\Validator;

class RefundOrder extends Validator
{

    protected $app = [
        'order_no',
        'valid_code',
    ];
}