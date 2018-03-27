<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class CardInfo extends Validator
{

    protected $app = [
        'password',
        'card_id',
		'is_bts',
		'is_bts_member',
    ];
}