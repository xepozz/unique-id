<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Debug;

use Yiisoft\User\Guest\GuestIdentityFactoryInterface;
use Yiisoft\User\Guest\GuestIdentityInterface;

final class GuestIdentityFactoryInterfaceProxy implements GuestIdentityFactoryInterface
{
    public function __construct(
        private readonly GuestIdentityFactoryInterface $decorated,
        private readonly IDCollector $collector,
    ) {
    }

    public function create(): GuestIdentityInterface
    {
        $identity = $this->decorated->{__FUNCTION__}(...func_get_args());
        $this->collector->collect($identity);
        return $identity;
    }
}
