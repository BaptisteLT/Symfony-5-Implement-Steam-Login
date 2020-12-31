<?php
namespace App\Security\Factory;

use App\Security\Firewall\SteamAuthListener;
use Symfony\Component\DependencyInjection\Reference;
use App\Security\Firewall\Authentication\SteamProvider;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class SteamFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, string $id, array $config, string $userProvider, ?string $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.steam.'.$id;
        $container
            ->setDefinition($providerId, new ChildDefinition(SteamProvider::class))
            ->setArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.steam.'.$id;
        $container->setDefinition($listenerId, new ChildDefinition(SteamAuthListener::class));

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'steam';
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}