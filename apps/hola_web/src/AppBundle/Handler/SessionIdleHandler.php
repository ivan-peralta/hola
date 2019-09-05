<?php

namespace AppBundle\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SessionIdleHandler
{
    protected $session;
    protected $tokenStorage;
    protected $router;
    protected $maxIdleTime;
    public function __construct(
        SessionInterface $session,
        TokenStorageInterface $tokenStorage,
        RouterInterface $router,
        $maxIdleTime = 0
    ) {
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->maxIdleTime = $maxIdleTime;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()
            || $this->maxIdleTime <= 0) {
            return;
        }

        $session = $this->session;
        $session->start();

        if ((time() - $session->getMetadataBag()->getLastUsed()) <= $this->maxIdleTime) {
            return;
        }

        $this->tokenStorage->setToken();
        $session->getFlashBag()->set('info', 'You have been logged out due to inactivity.');

        $event->setResponse(new RedirectResponse($this->router->generate('app_login')));
    }
}
