<?php declare(strict_types=1);

namespace AventuxB2BTools\Exception;

use Shopware\Core\Checkout\Customer\CustomerException;
use Symfony\Component\HttpFoundation\Response;

class CustomerNeedApprove extends CustomerException
{
    public const CUSTOMER_NEED_APPROVE = 'CHECKOUT__CUSTOMER_NEED_APPROVE';

    public function __construct(
        string $id,
        int $statusCode = Response::HTTP_UNAUTHORIZED,
        string $errorCode = self::CUSTOMER_NEED_APPROVE,
    ) {
        parent::__construct(
            $statusCode,
            $errorCode,
            'The customer with the id "{{ customerId }}" need approved by admin.',
            ['customerId' => $id]
        );
    }

    public function getSnippetKey(): string
    {
        return 'account.customerNeedApprove';
    }
}
