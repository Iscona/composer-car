<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class CardType extends Validator
{
	protected $app = [
		'is_bts',
		'is_bts_member',
    ];
}