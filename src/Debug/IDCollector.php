<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Debug;

use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;

final class IDCollector implements CollectorInterface
{
    use CollectorTrait;

    private array $identities = [];

    public function getCollected(): array
    {
        return [
            'identities' => $this->identities,
        ];
    }

    public function collect(
        IdentityInterface $identity,
    ): void {
        $this->identities[] = $identity;
    }
}
