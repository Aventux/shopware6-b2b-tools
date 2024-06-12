<?php declare(strict_types=1);

namespace AventuxB2BTools\Subscriber;

use AventuxB2BTools\Exception\CustomerNeedApprove;
use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomerLoginSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CustomerLoginEvent::class => [
                ['onCustomerLogin', 99999],
            ],
        ];
    }

    public function onCustomerLogin(CustomerLoginEvent $event): void
    {
        $customer = $event->getCustomer();
        $approved = $customer->getCustomFields()['aventux_b2b_tools_customer_approved'] ?? false;
        if ($approved !== true) {
            throw new CustomerNeedApprove($customer->getId());
        }
    }
}
