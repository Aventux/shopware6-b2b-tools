<?php declare(strict_types=1);

namespace AventuxB2BTools\Decorator\Storefront\Controller;

use AventuxB2BTools\Exception\CustomerHasNotBeenApproved;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\AuthController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerDecorator extends AuthController
{
    public function __construct(private readonly AuthController $authController)
    {
    }

    public function loginPage(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        return $this->authController->loginPage($request, $data, $context);
    }

    public function guestLoginPage(Request $request, SalesChannelContext $context): Response
    {
        return $this->authController->guestLoginPage($request, $context);
    }

    public function logout(Request $request, SalesChannelContext $context, RequestDataBag $dataBag): Response
    {
        return $this->authController->logout($request, $context, $dataBag);
    }

    public function login(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        try {
            return $this->authController->login($request, $data, $context);
        } catch (CustomerHasNotBeenApproved $e) {
            $errorSnippet = $e->getSnippetKey();
        }

        return $this->forwardToRoute(
            'frontend.account.login.page',
            [
                'loginError' => true,
                'errorSnippet' => $errorSnippet ?? null,
                'waitTime' => $waitTime ?? null,
            ]
        );
    }

    public function recoverAccountForm(Request $request, SalesChannelContext $context): Response
    {
        return $this->authController->recoverAccountForm($request, $context);
    }

    public function generateAccountRecovery(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        return $this->authController->generateAccountRecovery($request, $data, $context);
    }

    public function resetPasswordForm(Request $request, SalesChannelContext $context): Response
    {
        return $this->authController->resetPasswordForm($request, $context);
    }

    public function resetPassword(RequestDataBag $data, SalesChannelContext $context): Response
    {
        return $this->authController->resetPassword($data, $context);
    }
}
