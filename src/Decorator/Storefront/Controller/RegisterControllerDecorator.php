<?php declare(strict_types=1);

namespace AventuxB2BTools\Decorator\Storefront\Controller;

use AventuxB2BTools\Exception\CustomerNeedApprove;
use Shopware\Core\Framework\Validation\DataBag\QueryDataBag;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\RegisterController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterControllerDecorator extends RegisterController
{
    public function __construct(private readonly RegisterController $registerController)
    {
    }

    public function accountRegisterPage(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        return $this->registerController->accountRegisterPage($request, $data, $context);
    }

    /**
     * @throws \JsonException
     */
    public function customerGroupRegistration(string $customerGroupId, Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        return $this->registerController->customerGroupRegistration($customerGroupId, $request, $data, $context);
    }

    public function checkoutRegisterPage(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        return $this->registerController->checkoutRegisterPage($request, $data, $context);
    }

    public function register(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        try {
            return $this->registerController->register($request, $data, $context);
        } catch (CustomerNeedApprove $e) {
            $this->addFlash(self::INFO, $this->trans('account.customerNeedApprove'));
            return $this->redirectToRoute('frontend.account.register.page');
        }
    }

    public function confirmRegistration(SalesChannelContext $context, QueryDataBag $queryDataBag): Response
    {
        return $this->registerController->confirmRegistration($context, $queryDataBag);
    }
}
