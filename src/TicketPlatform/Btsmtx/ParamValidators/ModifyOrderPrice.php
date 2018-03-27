<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class ModifyOrderPrice extends Validator
{

    protected $app = [
        'order_no',
        'app_pric',
        'balance_pric'
    ];
}