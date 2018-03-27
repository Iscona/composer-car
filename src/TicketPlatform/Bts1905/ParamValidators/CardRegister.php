<?php

namespace biqu\TicketPlatform\Bts1905\ParamValidators;

use biqu\TicketPlatform\Bts1905\ParamValidators\Validator;

class CardRegister extends Validator
{

    protected $app = [
        'password',
        'mobile_phone',
        'id_num',
        'member_name',
        'balance',
        'member_type_no',
        'gender'
    ];
}