<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class GetOrderStatus extends Validator
{

    protected $app = [
        'serial_num',
        'app_seat_no',
    ];
}