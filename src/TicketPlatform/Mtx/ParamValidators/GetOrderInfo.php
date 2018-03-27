<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class GetOrderInfo extends Validator
{

    protected $app = [
        'order_no',
        'valid_code',
        'request_type'
    ];
}