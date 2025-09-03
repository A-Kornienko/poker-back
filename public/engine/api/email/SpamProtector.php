<?php

declare(strict_types=1);

namespace api\email;

class SpamProtector
{
    public function validateEmailAddress(string $email): array
    {
        return [];
    }
}
