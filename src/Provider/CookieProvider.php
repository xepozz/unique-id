<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Provider;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Xepozz\UniqueID\IdProviderInterface;
use Yiisoft\Cookies\Cookie;

final class CookieProvider implements IdProviderInterface
{
    public function __construct(
        private readonly string $cookieName = 'user_id',
    ) {
    }

    public function get(ServerRequestInterface $request): string
    {
        $cookie = $request->getCookieParams()[$this->cookieName] ?? null;
        if ($cookie === null) {
            throw new \RuntimeException(sprintf('Cookie "%s" not found', $this->cookieName));
        }
        return $cookie;
    }

    public function has(ServerRequestInterface $request): bool
    {
        return isset($request->getCookieParams()[$this->cookieName]);
    }

    public function wrap(ResponseInterface $response, string $uniqueUserID): ResponseInterface
    {
        if (!class_exists(Cookie::class)) {
            throw new \RuntimeException(
                sprintf(
                    'Class "%s" not found. Please install yiisoft/cookies package',
                    Cookie::class,
                )
            );
        }
        return (new Cookie($this->cookieName, $uniqueUserID))
            ->addToResponse($response);
    }
}