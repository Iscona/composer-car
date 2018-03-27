<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

class CardTransRecord extends Validator
{

    protected $app = [
        'password',
        'card_id',
        'start_date',
        'end_date',
        'type',
    ];

    protected $nullable = [];
}