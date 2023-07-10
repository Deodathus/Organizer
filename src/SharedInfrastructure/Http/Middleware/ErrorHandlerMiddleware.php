<?php
declare(strict_types=1);

namespace App\SharedInfrastructure\Http\Middleware;

use App\Modules\Authentication\Application\Exception\ExternalUserDoesNotExist;
use App\SharedInfrastructure\Http\Response\ValidationErrorResponse;
use Assert\LazyAssertionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class ErrorHandlerMiddleware implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof LazyAssertionException) {
            $event->setResponse(
                new JsonResponse(
                    [
                        'errors' => ValidationErrorResponse::getResponseContent(...$exception->getErrorExceptions())
                    ],
                    Response::HTTP_BAD_REQUEST,
                )
            );
        } else if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious();

            $statusCode = match ($exception::class) {
                ExternalUserDoesNotExist::class => Response::HTTP_NOT_FOUND,
                default => Response::HTTP_INTERNAL_SERVER_ERROR,
            };

            $event->setResponse(
                new JsonResponse(
                    [
                        'errors' => [
                            $exception->getMessage()
                        ],
                    ],
                    $statusCode
                )
            );
        } else {
            $event->setResponse(
                new JsonResponse(
                    [
                        'errors' => [
                            $exception->getMessage()
                        ],
                    ],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                )
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
