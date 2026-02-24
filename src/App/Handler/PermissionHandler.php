<?php

declare(strict_types=1);

namespace App\Handler;

use App\Provider\TokenDataProvider;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute\Route;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

#[Route(httpMethod: HttpMethod::GET, uri: '/has_permission/{token}')]
final class PermissionHandler implements HandlerInterface
{
    private const PERMISSION_READ = 'read';

    public function __construct(
        private readonly TokenDataProvider $tokenDataProvider,
    ) {}

    public function __invoke(
        ServerRequestInterface $serverRequest,
        RouteParameters $parameters,
    ): ResponseInterface {
        $tokenId = (string) $parameters->get('token', '');

        if ($tokenId === '') {
            return $this->respond(false, 400);
        }

        $token = $this->findTokenById($tokenId);

        if ($token === null) {
            return $this->respond(false, 403);
        }

        $hasPermission = \in_array(
            self::PERMISSION_READ,
            $token['permissions'] ?? [],
            true,
        );

        return $this->respond($hasPermission, 200);
    }

    /**
     * @return array{token:string, permissions?: list<string>}|null
     */
    private function findTokenById(string $tokenId): ?array
    {
        $tokens = $this->tokenDataProvider->getTokens();

        foreach ($tokens as $token) {
            if (($token['token'] ?? null) === $tokenId) {
                return $token;
            }
        }

        return null;
    }

    private function respond(bool $hasPermission, int $statusCode): ResponseInterface
    {
        return new JSONResponse(
            [
                'permission' => $hasPermission,
            ],
            $statusCode,
        );
    }
}
