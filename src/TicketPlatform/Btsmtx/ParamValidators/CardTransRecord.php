<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class CardTransRecord extends Validator
{

    protected $app = [
        'password',
        'card_id',
        'start_date',
        'end_date',
        'type',
		'is_bts',
		'is_bts_member',
    ];

    protected $nullable = [];
}