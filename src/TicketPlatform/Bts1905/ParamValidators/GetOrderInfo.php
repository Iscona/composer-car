<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class GetOrderInfo extends Validator
{

    protected $app = [
        'order_no',
    ];
}