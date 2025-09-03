<?php

namespace api\email;

/**
 * Шаблон builder, сначала собираем тело, потом отправляем письмо
 * Тело и тему письма можно передать в конструктор, а также в саму функцию отправки почты.
 * 1. $this->buildMailBody()->sent(); - составление и отправка письма.
 * 2. $this->sendMail() - отправка письма с внешними параметрами сообщения и темы.
 */
class MailController
{
    protected const DEFAULT_EMAIL_FROM = 'info@casino-sender.com';

    protected const URL_EMAIL_PROXY = 'http://api.dashamail.com/';

    protected const MAIL_TEMPLATE = [
        1  => 'register',
        2  => 'remind',
        4  => 'payin',
        5  => 'payout_success',
        7  => 'payout_none',
        8  => 'activate',
        9  => 'support',
        10 => 'tournament_start',
        11 => 'tournament_end'
    ];

    protected SpamProtector $spamProtector;

    public function __construct(
        protected ?string $mailBody = null,
        protected ?string $mailSubject = null,
        protected ?string $replyMail = null,
        protected ?array $userInfo = null,
    ) {
        $this->spamProtector = new SpamProtector();
    }

    /**
     * Отправка письма.
     */
    public function sendMail(
        ?string $from = null,
        ?string $to = null,
        ?string $subject = null,
        ?string $mailBody = null,
        ?string $replyMail = null
    ): ?\stdClass {
        return new \stdClass();
    }

    /**
     * Построение тела и темы письма.
     */
    public function buildMailBody(int $mailId, ?int $userId = null, ?array $tags = null, ?string $replyMail = null): static
    {
        return $this;
    }

    /**
     * Заполняем шаблон из файла реальными данными
     */
    public function replaceEmailBodyFromFile(string $templateName): static
    {
        return $this;
    }

    /**
     * Заполняем теги
     */
    public function replaceTagsInBodyMail(?array $tags = null): static
    {
        return $this;
    }

    /**
     * Заменяем данные из конфигурации
     */
    public function replaceConfigsInBodyMail(): static
    {
        return $this;
    }

    /**
     * Заменяем данные о пользователе
     */
    public function replaceUserInfoInBodyMail(): static
    {
        return $this;
    }

    public function replaceDomainInfoInBodyMail(): static
    {
        return $this;
    }
}
