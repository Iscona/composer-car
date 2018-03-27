<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class CardRecharge extends Validator
{
    protected $app = [
        'card_id',
        'password',
        'type',
        'price',
        'explain',
        'partner_id',
		'remarks',
		'is_bts',
		'is_bts_member',
    ];

    protected $nullable = ['partner_id'];
}