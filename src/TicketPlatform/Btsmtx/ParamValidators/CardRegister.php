<?php

namespace biqu\TicketPlatform\Btsmtx\ParamValidators;

use biqu\TicketPlatform\Btsmtx\ParamValidators\Validator;

class CardRegister extends Validator
{

    protected $app = [
        'password',
        'mobile_phone',
        'id_num',
        'member_name',
        'balance',
        'score',
        'member_type_no',
        'is_bts_member',
        'is_bts',
    ];

    protected $nullable = ['member_type_no'];
}