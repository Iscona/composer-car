<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class CardInfo extends Validator
{

    protected $app = [
        'password',
        'card_id',
        'mobile_phone'
    ];
}