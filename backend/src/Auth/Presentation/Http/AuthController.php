<?php

namespace App\Auth\Presentation\Http;

use App\Auth\Infrastructure\GoogleTokenVerifier;
use App\Shared\Infrastructure\Security\JwtService;
use App\Users\Domain\Entity\User;
use App\Users\Domain\UserRepository;
use App\Users\Domain\ValueObject\Email;
use App\Users\Domain\ValueObject\UserId;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[OA\Tag(name: 'Auth')]
final class AuthController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly GoogleTokenVerifier $google,
        private readonly JwtService $jwt,
        private readonly Security $security,
    ) {}

    #[Route(path: '/auth/google', name: 'auth_google', methods: ['POST'])]
    #[OA\Post(path: '/auth/google', summary: 'Login or register using Google ID token')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['idToken'], properties: [
        new OA\Property(property: 'idToken', type: 'string'),
    ], type: 'object'))]
    #[OA\Response(response: 200, description: 'Authenticated', content: new OA\JsonContent(properties: [
        new OA\Property(property: 'token', type: 'string'),
        new OA\Property(property: 'user', properties: [
            new OA\Property(property: 'id', type: 'string', format: 'uuid'),
            new OA\Property(property: 'email', type: 'string', format: 'email'),
            new OA\Property(property: 'displayName', type: 'string', nullable: true),
            new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
        ], type: 'object')
    ], type: 'object'))]
    public function google(Request $request): Response
    {
        $data = json_decode($request->getContent() ?: '{}', true);
        if (!is_array($data) || !isset($data['idToken']) || !is_string($data['idToken']) || $data['idToken'] === '') {
            return new JsonResponse(['error' => 'idToken is required'], Response::HTTP_BAD_REQUEST);
        }
        $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? $_SERVER['GOOGLE_CLIENT_ID'] ?? null;
        if (!$clientId) {
            return new JsonResponse(['error' => 'Server not configured for Google login'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        try {
            $payload = $this->google->verify($data['idToken'], $clientId);
            $googleId = $payload['sub'];
            $emailStr = isset($payload['email']) && is_string($payload['email']) ? $payload['email'] : null;
            $name = isset($payload['name']) && is_string($payload['name']) ? $payload['name'] : null;

            $user = $this->users->findByGoogleId($googleId);
            if (!$user && $emailStr) {
                $user = $this->users->findByEmail(Email::fromString($emailStr));
            }
            if (!$user) {
                $id = UserId::v4();
                $email = Email::fromString($emailStr ?? ($googleId . '@example.local'));
                $user = User::register($id, $email, $name, $googleId);
            } else {
                // Ensure googleId and name are up to date
                if (!$user->googleId()) {
                    $user->linkGoogleId($googleId);
                }
                if ($name && $user->displayName() !== $name) {
                    $user->rename($name);
                }
            }
            $this->users->save($user);

            $token = $this->jwt->createToken($user);
            return new JsonResponse([
                'token' => $token,
                'user' => [
                    'id' => (string) $user->id(),
                    'email' => (string) $user->email(),
                    'displayName' => $user->displayName(),
                    'roles' => array_map(fn($r) => $r->value, $user->roles()),
                ],
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Google authentication failed'], Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route(path: '/me', name: 'auth_me', methods: ['GET'])]
    #[OA\Get(path: '/me', summary: 'Get current authenticated user')]
    #[OA\Response(response: 200, description: 'Current user', content: new OA\JsonContent(properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'displayName', type: 'string', nullable: true),
        new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
    ], type: 'object'))]
    #[OA\Response(response: 401, description: 'Unauthorized')]
    public function me(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        // In our authenticator we return SecurityUser
        if (method_exists($user, 'domain')) {
            $domain = $user->domain();
            return new JsonResponse([
                'id' => (string) $domain->id(),
                'email' => (string) $domain->email(),
                'displayName' => $domain->displayName(),
                'roles' => array_map(fn($r) => $r->value, $domain->roles()),
            ]);
        }
        return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }
}
