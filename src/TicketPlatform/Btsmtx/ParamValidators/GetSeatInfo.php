<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class GetSeatInfo extends Validator
{

    protected $app = [
        'feature_app_no',
        'hall_no',
    ];
}