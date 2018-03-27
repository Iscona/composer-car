<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class RefundOrder extends Validator
{

    protected $app = [
        'order_no',
        'desc',
    ];
}