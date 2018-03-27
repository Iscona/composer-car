<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class CardScheduleDiscount extends Validator
{
    protected $app = [
        'card_id',
        'type',
        'feature_no',             //排期应用号
        'feature_date',
        'feature_time',
        'stand_price'
    ];
}