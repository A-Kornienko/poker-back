<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AccessDeniedListener implements EventSubscriberInterface
{
    public function onAccessDeniedException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $data       = [
            'success' => false,
            'msg'     => 'Access denied. ' . $exception->getMessage(),
            'data'    => []
        ];

        $response = new JsonResponse(
            $serializer->serialize($data, 'json'),
            403,
            ['Access-Control-Allow-Origin' => '*'],
            true
        );
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onAccessDeniedException', 2],
        ];
    }
}
