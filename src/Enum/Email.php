<?php

declare(strict_types=1);

namespace App\Enum;

enum Email: string
{
    case DefaultEmail = 'info@casino-sender.com';

    case UrlEmailProxy = 'http://api.dashamail.com/';
}
