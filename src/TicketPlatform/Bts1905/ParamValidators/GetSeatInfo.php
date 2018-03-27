<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class GetSeatInfo extends Validator
{

    protected $app = [
        'feature_app_no',
        'hall_no',
    ];
}