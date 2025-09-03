<?php

declare(strict_types=1);

namespace App\Exception;

use App\Helper\ErrorCodeHelper;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResponseException extends Exception
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, string $message = '', int $code = 0)
    {
        $this->translator = $translator;
        parent::__construct($message, $code);
    }

    public static function makeExceptionByCode(TranslatorInterface $translator, int $errorCode): self
    {
        $message           = ErrorCodeHelper::getErrorByCode($errorCode);
        $translatedMessage = $translator->trans($message);

        throw new ResponseException($translator, $translatedMessage, $errorCode);
    }
}
