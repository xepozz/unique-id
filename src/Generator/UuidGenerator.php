<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Generator;

use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Xepozz\UniqueID\IdGeneratorInterface;

final class UuidGenerator implements IdGeneratorInterface
{
    public function generate(ServerRequestInterface $request): string
    {
        return Uuid::uuid7()->toString();
    }
}