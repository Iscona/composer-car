<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class GetSeatInfo extends Validator
{

    protected $app = [
        'feature_app_no',
        'hall_no'
    ];
}