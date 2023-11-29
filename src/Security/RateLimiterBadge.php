<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

class RateLimiterBadge implements BadgeInterface
{
    private $resolved = false;
    /** @var RateLimit */
    private $limit = null;

    public function __construct(private Request $request)
    {
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

    public function markResolved()
    {
        return $this->resolved = true;
    }

    public function setRateLimit(RateLimit $limit)
    {
        $this->request->getSession()->set('auth.ratelimit', $limit);
        $this->limit = $limit;
    }

    public function getRateLimit()
    {
        return $this->limit;
    }
}
