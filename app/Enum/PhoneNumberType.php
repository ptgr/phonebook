<?php

namespace App\Enum;

enum PhoneNumberType: string 
{
    case WORK = 'work';
    case HOME = 'home';
    case PERSONAL = 'personal';
}