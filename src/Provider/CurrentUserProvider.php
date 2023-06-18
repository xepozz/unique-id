<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Provider;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Xepozz\UniqueID\IdProviderInterface;
use Yiisoft\User\CurrentUser;

final class CurrentUserProvider implements IdProviderInterface
{
    public function __construct(
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function get(ServerRequestInterface $request): string
    {
        $id = $this->currentUser->getId();
        if ($id === null) {
            throw new \RuntimeException('Cannot get current user ID');
        }

        return $id;
    }

    public function has(ServerRequestInterface $request): bool
    {
        if ($this->currentUser->isGuest()) {
            return false;
        }
        return $this->currentUser->getId() !== null;
    }

    public function wrap(ResponseInterface $response, string $uniqueUserID): ResponseInterface
    {
        return $response;
    }
}