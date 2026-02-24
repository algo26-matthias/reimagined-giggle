<?php

declare(strict_types=1);

namespace Test\Provider;

use App\Provider\TokenDataProvider;
use PHPUnit\Framework\TestCase;

class TokenDataProviderTest extends TestCase
{
    public function testGetTokens(): void
    {
        $dataProvider = new TokenDataProvider();

        $this->assertSame(
            [
                ['token' => 'token1234', 'permissions' => ['read', 'write']],
                ['token' => 'tokenReadonly', 'permissions' => ['read']],
            ],
            $dataProvider->getTokens()
        );
    }
}
