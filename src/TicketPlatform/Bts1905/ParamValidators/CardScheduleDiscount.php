<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class CardScheduleDiscount extends Validator
{
    protected $app = [
        'card_id',
        'password',
        'feature_no',             //排期应用号
        'stand_price'
    ];
}