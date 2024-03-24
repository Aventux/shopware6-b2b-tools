<?php declare(strict_types=1);

namespace AventuxB2BTools\Exception;

use Shopware\Core\Checkout\Customer\CustomerException;
use Symfony\Component\HttpFoundation\Response;

class CustomerHasNotBeenApproved extends CustomerException
{
    public const CUSTOMER_HAS_NOT_BEEN_APPROVED = 'CHECKOUT__CUSTOMER_HAS_NOT_BEEN_APPROVED';

    public function __construct(
        string $id,
        int $statusCode = Response::HTTP_UNAUTHORIZED,
        string $errorCode = self::CUSTOMER_HAS_NOT_BEEN_APPROVED,
    ) {
        parent::__construct(
            $statusCode,
            $errorCode,
            'The customer with the id "{{ customerId }}" has not been approved by admin.',
            ['customerId' => $id]
        );
    }

    public function getSnippetKey(): string
    {
        return 'account.customerHasNotBeenApproved';
    }
}
