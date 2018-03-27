<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class ModifyOrderPrice extends Validator
{

    protected $app = [
        'order_no',
        'app_pric',
        'balance_pric'
    ];
}