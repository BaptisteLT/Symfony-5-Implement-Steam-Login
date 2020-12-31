<?php
namespace App\Security\Firewall\Authentication;

use App\Token\SteamToken;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;

class SteamProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $client;

    public function __construct(UserProviderInterface $userProvider, HttpClientInterface $client)
    {
        $this->userProvider = $userProvider;
        $this->client = $client;
    }

    public function authenticate(TokenInterface $token)
    {
        if ($token->getAttribute('openid.ns') != "http://specs.openid.net/auth/2.0") {
            throw new AuthenticationException('Invalid Token');
        }
        
        $checkAuth = $token->getAttributes();
        
        $checkAuth['openid.mode'] = 'check_authentication';
        
        //Verify the authentication 
        $response = $this->client->request('GET', 'https://steamcommunity.com/openid/login', ['query' => $checkAuth]);
        
        
        dd($token->getUsername());
        if ((string)$response->getContent() == "ns:http://specs.openid.net/auth/2.0\nis_valid:true\n")
        {
            $user = $this->userProvider->loadUserByUsername($token->getUsername());
            $token->setUser($user);
            $token->setAuthenticated(true);
        }

        return $token;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof SteamToken;
    }
}
