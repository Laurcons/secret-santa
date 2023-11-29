<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class RateLimiterProtectionListener implements EventSubscriberInterface
{

    public function __construct(private RateLimiterFactory $loginLimiter)
    {
    }

    public function checkPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();
        if (!$passport->hasBadge(RateLimiterBadge::class)) {
            return;
        }

        /** @var RateLimiterBadge $badge */
        $badge = $passport->getBadge(RateLimiterBadge::class);
        if ($badge->isResolved()) {
            return;
        }

        /** @var UserBadge */
        $userBadge = $passport->getBadge(UserBadge::class);
        $nick = $userBadge->getUserIdentifier();

        $limiter = $this->loginLimiter->create($nick);
        $limit = $limiter->consume(1);
        $badge->setRateLimit($limit);
        dump($limit);

        $passport->setAttribute('ratelimit', $limit);

        if (!$limit->isAccepted())
            throw new AuthenticationException("Ai atins numărul maxim de încercări. Încearcă din nou în 5 minute.");

        $badge->markResolved();
    }

    public static function getSubscribedEvents(): array
    {
        return [CheckPassportEvent::class => ['checkPassport', 1024]];
    }
}
