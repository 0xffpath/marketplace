<?php
namespace App\Handler;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AutoLogoutHandler
{
    protected $session;
    protected $securityToken;
    protected $router;

    public function __construct(SessionInterface $session, TokenStorageInterface $securityToken, RouterInterface $router)
    {
        $this->session = $session;
        $this->securityToken = $securityToken;
        $this->router = $router;
    }

    /**
     * Check if user needs to be logged out
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        $this->session->start();

        $expireTime = $this->session->get('expire');
        if ($expireTime < time() && !empty($expireTime)) {
            $this->securityToken->setToken(null);
            $this->session->invalidate();
        }
    }
}
