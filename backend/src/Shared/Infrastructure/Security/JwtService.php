<?php

namespace App\Shared\Infrastructure\Security;

use App\Users\Domain\Entity\User;

final class JwtService
{
    public function __construct(private readonly string $secret, private readonly int $ttlSeconds = 3600)
    {
    }

    public static function fromEnv(): self
    {
        $secret = $_ENV['JWT_SECRET'] ?? $_SERVER['JWT_SECRET'] ?? null;
        if (!$secret) {
            throw new \RuntimeException('JWT_SECRET env var is not set');
        }
        $ttl = (int) ($_ENV['JWT_TTL'] ?? $_SERVER['JWT_TTL'] ?? 3600);
        return new self($secret, $ttl);
    }

    public function createToken(User $user): string
    {
        $now = time();
        $payload = [
            'iss' => 'tickets-manager',
            'sub' => (string) $user->id(),
            'email' => (string) $user->email(),
            'roles' => array_map(fn($r) => $r->value, $user->roles()),
            'iat' => $now,
            'exp' => $now + $this->ttlSeconds,
        ];
        return $this->encode($payload);
    }

    public function encode(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $segments = [
            $this->b64(json_encode($header, JSON_UNESCAPED_SLASHES)),
            $this->b64(json_encode($payload, JSON_UNESCAPED_SLASHES)),
        ];
        $signature = hash_hmac('sha256', implode('.', $segments), $this->secret, true);
        $segments[] = $this->b64($signature);
        return implode('.', $segments);
    }

    public function decode(string $token, bool $verifyExp = true): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \RuntimeException('Invalid JWT format');
        }
        [$h64, $p64, $s64] = $parts;
        $header = json_decode($this->b64d($h64), true);
        $payload = json_decode($this->b64d($p64), true);
        $sig = $this->b64d($s64);
        if (!is_array($header) || ($header['alg'] ?? null) !== 'HS256') {
            throw new \RuntimeException('Unsupported JWT alg');
        }
        $expected = hash_hmac('sha256', $h64 . '.' . $p64, $this->secret, true);
        if (!hash_equals($expected, $sig)) {
            throw new \RuntimeException('Invalid JWT signature');
        }
        if ($verifyExp && isset($payload['exp']) && time() >= (int) $payload['exp']) {
            throw new \RuntimeException('JWT expired');
        }
        return $payload;
    }

    private function b64(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function b64d(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
