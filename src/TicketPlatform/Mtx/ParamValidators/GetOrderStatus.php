<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class GetOrderStatus extends Validator
{

    protected $app = [
        'serial_num',
    ];
}