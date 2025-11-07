<?php

namespace App\Auth\Infrastructure;

final class GoogleTokenVerifier
{
    /**
     * Verifies a Google ID token via tokeninfo endpoint and returns payload array.
     * Throws \RuntimeException on any validation error.
     *
     * @return array{sub:string,email?:string,email_verified?:string,name?:string,picture?:string,aud?:string}
     */
    public function verify(string $idToken, string $expectedClientId): array
    {
        $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 5,
            ],
        ]);
        $json = @file_get_contents($url, false, $ctx);
        if ($json === false) {
            throw new \RuntimeException('Failed to verify Google token');
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new \RuntimeException('Invalid Google token response');
        }
        if (($data['aud'] ?? null) !== $expectedClientId) {
            throw new \RuntimeException('Google token audience mismatch');
        }
        if (!isset($data['sub']) || !is_string($data['sub'])) {
            throw new \RuntimeException('Google token missing subject');
        }
        return $data;
    }
}
