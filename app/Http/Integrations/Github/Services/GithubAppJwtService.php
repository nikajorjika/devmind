<?php

namespace App\Http\Integrations\Github\Services;
use Firebase\JWT\JWT;

class GithubAppJwtService
{
    public function generate(): string
    {
        $privateKey = config('services.github.private_key');

        $payload = [
            'iat' => time(),
            'exp' => time() + 600, // 10 minutes
            'iss' => config('services.github.app_id'),
        ];

        return JWT::encode($payload, $privateKey, 'RS256');
    }
}
