<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class CardInfo extends Validator
{

    protected $app = [
        'password',
        'card_id',
    ];
}