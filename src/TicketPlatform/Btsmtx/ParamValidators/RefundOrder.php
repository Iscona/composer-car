<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class RefundOrder extends Validator
{

    protected $app = [
        'order_no',
        'desc',
    ];
}