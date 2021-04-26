<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
class GithubUserProvider implements UserProviderInterface
{

    private $client;
    private $serializer;

    public function __construct(Client $client, Serializer $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer; 
    }


    public function loadUserByUsername($username)
    {
        $url = 'https://api.github.com/user?access_token='.$username;

        $response = $this->client->get($url);
        $res = $response->getBody()->getContents();

        $userData = $this->serializer->deserialize($res, 'array', 'json');

        if(!$userData) {
            throw new \LogicException('Did not managed to get your user info from github');
        }

        return new User($userData['login'], $userData['name'], $userData['email'], $userData['avatar_url'], $userData['html_url']);
    }

    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        $class = get_class($user);

        if(!$this->supportsClass($class)) {
            throw new UnsupportedUserException();
        }
    }

    public function supportsClass($class)
    {
        return 'AppBundle\Entity\User' === $class;
    }
}