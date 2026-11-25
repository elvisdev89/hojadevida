<?php

namespace App\Enums;

enum IdentificationType: string
{
    case CitizenshipCard = 'CC';
    case ForeignerId = 'CE';
    case IdentityCard = 'TI';
    case Passport = 'PA';
    case TemporaryProtectionPermit = 'PPT';
}
