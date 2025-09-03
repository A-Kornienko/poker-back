<?php

declare(strict_types=1);

namespace App\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractHandler
{
    protected const REQUEST_PAGE  = 'page';
    protected const REQUEST_LIMIT = 'limit';
    protected const REQUEST_RULE  = 'rule';
    protected const REQUEST_TYPE  = 'type';

    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
    ) {
        global $language;
        $this->translator->setLocale('ru');
    }

    protected function getJsonParam(
        Request $request,
        ?string $key = null,
        mixed $default = null
    ): mixed {
        $content = $request->getContent();
        $json    = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $json = [];
        }

        if (!$key) {
            return $json;
        }

        return $json[$key] ?? $default;
    }

    protected function getCollectionFromJson(string $json, bool $parseStr = false): ArrayCollection
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new ArrayCollection();
        }

        if ($parseStr) {
            parse_str($data, $data);
        }

        return new ArrayCollection($data);
    }
}
