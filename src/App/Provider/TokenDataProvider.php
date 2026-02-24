<?php

declare(strict_types=1);

namespace App\Provider;

/**
 * !! Do not change this file !!
 * The purpose of this provider is to simulate getting 
 * available tokens including their permissions from somewhere (most likely a DB or microservice).
 */
class TokenDataProvider
{
    private const TOKENS = [
        ['token' => 'token1234', 'permissions' => ['read', 'write']],
        ['token' => 'tokenReadonly', 'permissions' => ['read']],
    ];

    /**
     * @return array<array{token: string, permissions: string[]}>
     */
    public function getTokens(): array
    {
        return self::TOKENS;
    }
}
