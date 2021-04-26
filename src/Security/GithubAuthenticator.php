<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class GithubAuthenticator implements SimpleAuthenticatorInterface, AuthenticationFailureHandlerInterface
{


    public function createToken(Request $request, $providerKey)
    {
        $bearer = $request->headers->get('Authorization');
        $accessToken = substr($bearer, 7);

        return new PreAuthenticatedToken('annon.', $accessToken, $providerKey);
    }

    public function authenticateToken(\Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider, $providerKey)
    {
        $accessToken = $token->getCredentials();
        $user = $userProvider->loadUserByUsername($accessToken);

        return new PreAuthenticatedToken($user, $accessToken, $providerKey, ['ROLE_USER']);
    }

    public function supportsToken(\Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() == $providerKey;
    }

    public function onAuthenticationFailure(\Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception)
    {
        return new Response("Authentication Failed :(", 401);
    }
}