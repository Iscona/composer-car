<?php

namespace biqu\TicketPlatform\Mtx\ParamValidators;

use biqu\TicketPlatform\Mtx\ParamValidators\Validator;

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