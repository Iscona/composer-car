<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class GetOrderStatus extends Validator
{

    protected $app = [
        'serial_num',
    ];
}