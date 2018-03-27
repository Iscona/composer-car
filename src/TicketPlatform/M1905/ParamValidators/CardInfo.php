<?php

namespace biqu\TicketPlatform\M1905\ParamValidators;

use biqu\TicketPlatform\M1905\ParamValidators\Validator;

class CardInfo extends Validator
{

    protected $app = [
        'password',
        'card_id',
        'mobile_phone'
    ];
}