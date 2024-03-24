<?php declare(strict_types=1);

namespace AventuxB2BTools\Decorator;

use AventuxB2BTools\Exception\CustomerHasNotBeenApproved;
use Shopware\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\CustomerException;
use Shopware\Core\Checkout\Customer\Event\CustomerBeforeLoginEvent;
use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Checkout\Customer\Exception\AddressNotFoundException;
use Shopware\Core\Checkout\Customer\Exception\BadCredentialsException;
use Shopware\Core\Checkout\Customer\Exception\CustomerNotFoundByIdException;
use Shopware\Core\Checkout\Customer\Exception\CustomerNotFoundException;
use Shopware\Core\Checkout\Customer\Exception\CustomerOptinNotCompletedException;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Uuid\Exception\InvalidUuidException;
use Shopware\Core\System\SalesChannel\Context\CartRestorer;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[Package('checkout')]
class AccountServiceDecorator extends AccountService
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly EntityRepository $customerRepository,
        private readonly CartRestorer $restorer,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    /**
     * @throws CustomerNotLoggedInException
     * @throws InvalidUuidException
     * @throws AddressNotFoundException
     */
    public function setDefaultBillingAddress(string $addressId, SalesChannelContext $context, CustomerEntity $customer): void
    {
        $this->accountService->setDefaultBillingAddress($addressId, $context, $customer);
    }

    /**
     * @throws CustomerNotLoggedInException
     * @throws InvalidUuidException
     * @throws AddressNotFoundException
     */
    public function setDefaultShippingAddress(string $addressId, SalesChannelContext $context, CustomerEntity $customer): void
    {
        $this->accountService->setDefaultShippingAddress($addressId, $context, $customer);
    }

    /**
     * @deprecated tag:v6.7.0 - Method will be removed, use `AccountService::loginById` or `AccountService::loginByCredentials` instead
     *
     * @throws BadCredentialsException
     * @throws CustomerNotFoundException
     */
    public function login(string $email, SalesChannelContext $context, bool $includeGuest = false): string
    {
        return $this->accountService->login($email, $context, $includeGuest);
    }

    /**
     * @throws BadCredentialsException
     * @throws CustomerNotFoundByIdException
     */
    public function loginById(string $id, SalesChannelContext $context): string
    {
        return $this->accountService->loginById($id, $context);
    }

    /**
     * @throws CustomerNotFoundException
     * @throws BadCredentialsException
     * @throws CustomerOptinNotCompletedException
     */
    public function loginByCredentials(string $email, string $password, SalesChannelContext $context): string
    {
        if ($email === '' || $password === '') {
            throw CustomerException::badCredentials();
        }

        $event = new CustomerBeforeLoginEvent($context, $email);
        $this->eventDispatcher->dispatch($event);

        $customer = $this->getCustomerByLogin($email, $password, $context);

        return $this->loginByCustomer($customer, $context);
    }

    /**
     * @throws CustomerNotFoundException
     * @throws BadCredentialsException
     * @throws CustomerOptinNotCompletedException
     */
    public function getCustomerByLogin(string $email, string $password, SalesChannelContext $context): CustomerEntity
    {
        $customerByLogin = $this->accountService->getCustomerByLogin($email, $password, $context);
        $approved = $customerByLogin->getCustomFields()['aventux_b2b_tools_customer_approved'] ?? false;
        if ($approved !== true) {
            throw new CustomerHasNotBeenApproved($customerByLogin->getId());
        }

        return $customerByLogin;
    }

    private function loginByCustomer(CustomerEntity $customer, SalesChannelContext $context): string
    {
        $this->customerRepository->update([
            [
                'id' => $customer->getId(),
                'lastLogin' => new \DateTimeImmutable(),
            ],
        ], $context->getContext());

        $context = $this->restorer->restore($customer->getId(), $context);
        $newToken = $context->getToken();

        $event = new CustomerLoginEvent($context, $customer, $newToken);
        $this->eventDispatcher->dispatch($event);

        return $newToken;
    }
}
