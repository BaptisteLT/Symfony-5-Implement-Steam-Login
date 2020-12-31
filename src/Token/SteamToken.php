<?php
namespace App\Token;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SteamToken implements TokenInterface
{
    private $user;
    private $username;
    private $authenticated;
    private $attributes = [];
    private $roleNames = [];

    /**
     * @param string[] $roles An array of roles
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $roles = [])
    {
        foreach ($roles as $role) {
            $this->roleNames[] = $role;
        }
    }



    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleNames(): array
    {
        return $this->roleNames;
    }

    public function setRoles(array $roles): self/*Méthode ajoutée*/
    {
        $this->roles = $roles;

        return $this;
    }

    public function getCredentials()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user):self
    {
        if (!($user instanceof UserInterface || (\is_object($user) && method_exists($user, '__toString')) || \is_string($user))) {
            throw new \InvalidArgumentException('$user must be an instanceof UserInterface, an object implementing a __toString method, or a primitive string.');
        }

        $this->user = $user;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        //If user we return the user username, otherwise we return the username in the token
        return $this->user ? $this->user->getUsername() : $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return $this->authenticated;
    }
    /**
     * {@inheritdoc}
     */
    public function setAuthenticated(bool $authenticated)
    {
        $this->authenticated = $authenticated;
    }

    public function eraseCredentials()
    {

    }

    /**
     * Returns the token attributes.
     *
     * @return array The token attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = [];
        foreach ($attributes as $key => $attribute) {
            $key = str_replace("openid_", "openid.", $key);
            $this->attributes[$key] = $attribute;
        }
        return $this;
    }

    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }


    public function __serialize(): array
    {
        return [
            'attributes' => $this->attributes,
            'authenticated' => $this->isAuthenticated(),
            'username' => $this->getUsername(),
            'roles' => $this->getRoleNames(),
            'user' => $this->getUser(),
        ];
    }

    public function __unserialize($data): void
    {
        $this->attributes = $data['attributes'];
        $this->setAuthenticated($data['authenticated']);
        $this->setUsername($data['username']);
        $this->setUser($data['user']);
        $this->roles = $data['roles'];
    }

    /**
     * @internal
     */
    final public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    /**
     * @internal
     */
    final public function unserialize($serialized)
    {
        $this->__unserialize(\is_array($serialized) ? $serialized : unserialize($serialized));
    }
}
