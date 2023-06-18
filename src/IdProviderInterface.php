<?php

declare(strict_types=1);

namespace Xepozz\UniqueID;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface IdProviderInterface
{
    public function get(ServerRequestInterface $request): string;

    public function has(ServerRequestInterface $request): bool;

    public function wrap(ResponseInterface $response, string $uniqueUserID): ResponseInterface;
}