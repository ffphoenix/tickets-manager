<?php

namespace App\Users\Domain;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case ORGANISER = 'organiser';
    case ADMIN = 'admin';
}
