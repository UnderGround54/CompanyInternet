<?php
namespace App\EventListener;
use App\Service\ResponseService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class AccessDeniedListener implements EventSubscriberInterface
{
    public function __construct(private readonly ResponseService $responseService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION  => 'onKernelException',
        ];
    }
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            $response = $this->responseService->error([],$exception->getMessage(), Response::HTTP_FORBIDDEN);

            $event->setResponse($response);
        }
    }
}