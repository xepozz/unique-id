<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Tests\Support;

use Psr\Http\Message\ServerRequestInterface;
use Xepozz\UniqueID\IdGeneratorInterface;

class StubGenerator implements IdGeneratorInterface
{
    public function __construct(private readonly ?string $id)
    {
    }

    public function generate(ServerRequestInterface $request): ?string
    {
        return $this->id;
    }
}