<?php

namespace App\Security;

use App\Entity\Participant;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class CustomLoginAuthenticator extends AbstractLoginFormAuthenticator
{

    public function __construct(
        private UrlGeneratorInterface $router,
        private RateLimiterFactory $loginLimiter
    ) {
    }

    public function supports(Request $request): bool
    {
        return $request->request->has('_username');
    }

    protected function getLoginUrl(Request $request): string
    {
        $nick = $request->request->get('_username');
        if ($nick)
            return $this->router->generate("app_login_login", ['nickname' => $nick]);
        return $this->router->generate("app_login_begin");
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');
        // $csrf = $request->request->get('csrf');

        $passport = new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                // new CsrfTokenBadge("auth", $csrf),
                // new RememberMeBadge(),
                new RateLimiterBadge($request),
            ]
        );
        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $request->getSession()->remove('auth.ratelimit');
        $nick = $token->getUserIdentifier();
        $this->loginLimiter->create($nick)->reset();

        return new RedirectResponse("/");
    }
}
