<?php

namespace App\Shared\Infrastructure\Security;

use App\Users\Domain\UserRepository;
use App\Users\Domain\ValueObject\UserId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly JwtService $jwt,
        private readonly UserRepository $users
    ) {}

    public function supports(Request $request): ?bool
    {
        $auth = $request->headers->get('Authorization');
        return is_string($auth) && str_starts_with($auth, 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $auth = $request->headers->get('Authorization');
        $token = substr((string) $auth, 7);
        try {
            $payload = $this->jwt->decode($token);
        } catch (\Throwable $e) {
            throw new AuthenticationException('Invalid token');
        }
        $userId = $payload['sub'] ?? null;
        if (!is_string($userId)) {
            throw new AuthenticationException('Invalid token payload');
        }
        $badge = new UserBadge($userId, function (string $identifier): UserInterface {
            $user = $this->users->get(UserId::fromString($identifier));
            return new SecurityUser($user);
        });
        return new SelfValidatingPassport($badge);
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null; // continue request
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }
}
