<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\ErrorCodeHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, StreamedResponse};
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class BaseApiController extends AbstractController
{
    public const DEFAULT_HEADER = [];

    public function __construct(
        protected Security $security,
        protected TranslatorInterface $translator,
    ) {
        global $language;

        $this->translator->setLocale($language);
    }

    protected function response(
        array $data,
        int $errorCode = 0,
        int $status = 200,
        array $headers = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'data'    => array_merge($data, ['isAuthorized' => (bool) $this->security->getUser()]),
        ];

        if ($errorCode) {
            $errorKey            = ErrorCodeHelper::getErrorByCode($errorCode);
            $translatedMessage   = $this->translator->trans($errorKey);
            $response['success'] = false;
            $response['error']   = $translatedMessage;
        }

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $headers    = array_merge(static::DEFAULT_HEADER, $headers);

        return new JsonResponse(
            $serializer->serialize($response, 'json'),
            $status,
            $headers,
            true
        );
    }

    protected function streamedResponse(callable $callback)
    {
        $response = new StreamedResponse();
        // Set SSE-specific headers
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response->setCallback($callback);
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

    protected function getCollectionFromJson(string $json, bool $parse_str = false): ArrayCollection
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new ArrayCollection();
        }

        if ($parse_str) {
            parse_str($data, $data);
        }

        return new ArrayCollection($data);
    }
}
