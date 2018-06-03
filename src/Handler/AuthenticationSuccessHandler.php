<?php
namespace App\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    public function __construct(HttpUtils $httpUtils, array $options)
    {
        parent::__construct($httpUtils, $options);
    }

    /**
     * Sets auto-logout time
     *
     * @param Request $request
     * @param TokenInterface $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $session = new Session();
        $session->set('expire', ($request->request->get('_auto')*60)+time());

        return parent::onAuthenticationSuccess($request, $token);
    }
}
