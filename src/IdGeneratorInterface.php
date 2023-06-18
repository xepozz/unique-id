<?php

declare(strict_types=1);

namespace Xepozz\UniqueID;

use Psr\Http\Message\ServerRequestInterface;

interface IdGeneratorInterface
{
    public function generate(ServerRequestInterface $request): ?string;
}