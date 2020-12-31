<?php
// src/EventListener/ExceptionListener.php
namespace App\Security\Firewall;

use App\Token\SteamToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SteamAuthListener
{
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var string Uniquely identifies the secured area
     */
    private $providerKey;

    /**
     * Default route
     *
     * @var string example 'home'
     */
    private $defaultRoute;

    public function __construct($defaultRoute = 'home',TokenStorageInterface $tokenStorage,AuthenticationManagerInterface $authenticationManager,RouterInterface $router)
    {
        $this->defaultRoute = $defaultRoute;
        $this->router = $router;
        $this->authenticationManager = $authenticationManager;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function __invoke(RequestEvent $event)
    {
        
        $request = $event->getRequest();
        if ($request->get('_route') != 'logincheck') {
            return;
        }
        
        $token = new SteamToken();
        $username = $request->query->get('openid_claimed_id');
        
        $username = str_replace("http://steamcommunity.com/openid/id/", "", $username);
        $username = str_replace("https://steamcommunity.com/openid/id/", "", $username);
        
        $token->setUsername($username);
        $token->setAttributes($request->query->all());
        
        //Bug sur cette ligne
        $authToken = $this->authenticationManager->authenticate($token);
        
        $this->tokenStorage->setToken($authToken);


        $response = new RedirectResponse($this->router->generate('steam_auth'));
        $event->setResponse($response);
    }
}