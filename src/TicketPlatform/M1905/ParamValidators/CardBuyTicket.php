<?php

namespace biqu\TicketPlatform\M1905\ParamValidators;

use biqu\TicketPlatform\M1905\ParamValidators\Validator;

class CardBuyTicket extends Validator
{

    protected $app = [
        'password',
        'card_id',
        'serial_num',
        'seat_info',
        'ticket_price',
        'handlingfee',
        'protected_price'
    ];

    protected $nullable = [
    ];
}