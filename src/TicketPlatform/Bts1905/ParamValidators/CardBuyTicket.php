<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class CardBuyTicket extends Validator
{

    protected $app = [
        'password',
        'card_id',
        'serial_num',
        'seat_info',
        'ticket_price',
        'handlingfee',
        'protected_price',
        'user_id',
    ];

    protected $nullable = [
    ];
}