<?php
declare(strict_types=1);

namespace App\SharedInfrastructure\Http\Middleware;

use App\Modules\Authentication\Application\Exception\ExternalUserDoesNotExist;
use App\Modules\Finance\Currency\Application\Exception\CurrencyWithGivenCodeAlreadyExistsException;
use App\Modules\Finance\Currency\Application\Exception\UnsupportedCurrencyCodeException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseBecauseCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseBecauseExpenseCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseBecauseWalletBalanceIsNotEnoughToProceedException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseToNonExistingWalletException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseWithInvalidCurrencyCodeException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseWithNonExistingCurrencyException;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCategoryDoesNotExistException;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCreatorDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\CannotRegisterTransferTransactionWithoutReceiverWalletIdException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyCodeIsNotSupportedException;
use App\Modules\Finance\Wallet\Application\Exception\InvalidTransactionTypeException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCreatorDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Wallet\Application\Exception\WalletBalanceIsNotEnoughToProceedTransactionException;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\SharedInfrastructure\Http\Response\ValidationErrorResponse;
use Assert\LazyAssertionException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final readonly class ErrorHandlerMiddleware implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof InvalidTransactionTypeException) {
            $event->setResponse(
                new JsonResponse(
                    [
                        'errors' => [
                            $exception->getMessage()
                        ],
                    ],
                    Response::HTTP_BAD_REQUEST
                )
            );
        } else if ($exception instanceof LazyAssertionException) {
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
                // expense - 401
                ExpenseCreatorDoesNotExistException::class,
                CannotRegisterExpenseBecauseCreatorDoesNotOwnWalletException::class,
                // wallet - 401
                TransactionCreatorDoesNotExistException::class,
                TransactionCreatorDoesNotOwnWalletException::class => Response::HTTP_UNAUTHORIZED,

                // expense - 400
                CannotRegisterExpenseWithInvalidCurrencyCodeException::class,
                CannotRegisterExpenseBecauseExpenseCurrencyIsDifferentWalletHasException::class,
                CannotRegisterExpenseBecauseWalletBalanceIsNotEnoughToProceedException::class,
                // wallet - 400
                InvalidTransactionTypeException::class,
                WalletBalanceIsNotEnoughToProceedTransactionException::class,
                TransactionCurrencyIsDifferentWalletHasException::class,
                CurrencyCodeIsNotSupportedException::class,
                CannotRegisterTransferTransactionWithoutReceiverWalletIdException::class,
                // currency - 400
                UnsupportedCurrencyCodeException::class => Response::HTTP_BAD_REQUEST,

                // expense - 404
                ExpenseCategoryDoesNotExistException::class,
                CannotRegisterExpenseToNonExistingWalletException::class,
                CannotRegisterExpenseWithNonExistingCurrencyException::class,
                // wallet - 404
                WalletDoesNotExistException::class,
                CurrencyDoesNotExistException::class,
                ExternalUserDoesNotExist::class => Response::HTTP_NOT_FOUND,

                // currency - 409
                CurrencyWithGivenCodeAlreadyExistsException::class => Response::HTTP_CONFLICT,

                default => Response::HTTP_INTERNAL_SERVER_ERROR,
            };

            if ($statusCode === Response::HTTP_INTERNAL_SERVER_ERROR) {
                $this->logger->error(
                    sprintf(
                        'Unknown error! Exception: "%s", Exception message: "%s"',
                        get_class($exception),
                        $exception->getMessage()
                    )
                );

                $event->setResponse(
                    new JsonResponse(
                        [
                            'errors' => [
                                'Internal Server Error',
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
                                $exception->getMessage(),
                            ],
                        ],
                        $statusCode
                    )
                );
            }
        } else {
            $this->logger->error(
                sprintf(
                    'Unknown error! Exception: "%s", Exception message: "%s"',
                    get_class($exception),
                    $exception->getMessage()
                )
            );

            $event->setResponse(
                new JsonResponse(
                    [
                        'errors' => [
                            'Internal Server Error'
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
