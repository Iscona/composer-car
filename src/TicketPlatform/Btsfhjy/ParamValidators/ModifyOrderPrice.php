<?php

namespace biqu\TicketPlatform\Btsfhjy\ParamValidators;

use biqu\TicketPlatform\Btsfhjy\ParamValidators\Validator;

class ModifyOrderPrice extends Validator
{

    protected $app = [
        'order_no',
        'app_pric',
        'balance_pric'
    ];
}