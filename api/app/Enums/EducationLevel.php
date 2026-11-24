<?php

namespace App\Enums;

enum EducationLevel: string
{
    case HighSchool = 'high_school';
    case Technical = 'technical';
    case Technologist = 'technologist';
    case Undergraduate = 'undergraduate';
    case Specialization = 'specialization';
    case Master = 'master';
    case Doctorate = 'doctorate';
}
