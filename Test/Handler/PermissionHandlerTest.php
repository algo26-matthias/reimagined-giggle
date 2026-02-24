<?php

declare(strict_types=1);

namespace Test\Handler;

use App\Handler\PermissionHandler;
use App\Provider\TokenDataProvider;
use PHPUnit\Framework\TestCase;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

class PermissionHandlerTest extends TestCase
{
    public function testHasReadPermission(): void
    {
        $handler = $this->createHandlerWithTokens([
            [
                'token' => 'abc',
                'permissions' => ['read', 'write'],
            ],
        ]);

        $params = $this->createRouteParameters('abc');
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $handler($request, $params);

        $this->assertSame(200, $response->getCode());

        $data = json_decode((string)$response->getContent(), true);
        $this->assertTrue($data['permission']);
    }

    public function testTokenWithoutReadPermission(): void
    {
        $handler = $this->createHandlerWithTokens([
            [
                'token' => 'abc',
                'permissions' => ['write'],
            ],
        ]);

        $params = $this->createRouteParameters('abc');
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $handler($request, $params);

        $this->assertSame(200, $response->getCode());

        $data = json_decode((string)$response->getContent(), true);
        $this->assertFalse($data['permission']);
    }

    public function testUnknownTokenReturns403(): void
    {
        $handler = $this->createHandlerWithTokens([
            [
                'token' => 'other',
                'permissions' => ['read'],
            ],
        ]);

        $params = $this->createRouteParameters('abc');
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $handler($request, $params);

        $this->assertSame(403, $response->getCode());

        $data = json_decode((string)$response->getContent(), true);
        $this->assertFalse($data['permission']);
    }

    public function testEmptyTokenReturns400(): void
    {
        $handler = $this->createHandlerWithTokens([]);

        $params = $this->createRouteParameters('');
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $handler($request, $params);

        $this->assertSame(400, $response->getCode());

        $data = json_decode((string)$response->getContent(), true);
        $this->assertFalse($data['permission']);
    }

    public function testPermissionsMissingDefaultsToFalse(): void
    {
        $handler = $this->createHandlerWithTokens([
            [
                'token' => 'abc'
            ],
        ]);

        $params = $this->createRouteParameters('abc');
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $handler($request, $params);

        $this->assertSame(200, $response->getCode());

        $data = json_decode((string)$response->getContent(), true);
        $this->assertFalse($data['permission']);
    }

    private function createHandlerWithTokens(array $tokens): PermissionHandler
    {
        $provider = $this->createMock(TokenDataProvider::class);
        $provider
            ->method('getTokens')
            ->willReturn($tokens);

        return new PermissionHandler($provider);
    }

    private function createRouteParameters(string $token): RouteParameters
    {
        $params = $this->createMock(RouteParameters::class);
        $params
            ->method('get')
            ->with('token', '')
            ->willReturn($token);

        return $params;
    }
}
