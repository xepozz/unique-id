<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Tests\Support;

use Yiisoft\Auth\IdentityInterface;

class StubIdentity implements IdentityInterface
{
    public function __construct(private readonly ?string $id)
    {
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}