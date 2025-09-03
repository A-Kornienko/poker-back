<?php

declare(strict_types=1);

namespace App\Handler\Chat;

use App\Entity\Table;
use App\Exception\ResponseException;
use App\Handler\AbstractHandler;
use App\Helper\ErrorCodeHelper;
use App\Service\ChatService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class SendMessagesChatHandler extends AbstractHandler
{
    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
        protected ChatService $chatService
    ) {
        parent::__construct($security, $translator);
    }

    protected function validateMessage(string $message): void
    {
        if (mb_strlen($message) > 255) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::LONG_MESSAGE);
        }

        if (empty($message)) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::EMPTY_MESSAGE);
        }
    }

    public function __invoke(?Table $table, Request $request): void
    {
        if (!$table) {
            ResponseException::makeExceptionByCode($this->translator, ErrorCodeHelper::TABLE_NOT_FOUND);
        }

        $message = $this->getJsonParam($request, 'message');
        $this->validateMessage($message);

        $this->chatService->create($table, trim($message), $this->security->getUser());
    }
}
