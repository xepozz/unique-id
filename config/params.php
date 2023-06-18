<?php

declare(strict_types=1);

use Xepozz\UniqueID\Generator\UuidGenerator;
use Xepozz\UniqueID\Provider\CookieProvider;
use Xepozz\UniqueID\Provider\CurrentUserProvider;
use Xepozz\UniqueID\Provider\HeaderProvider;
use Xepozz\UniqueID\Provider\SessionProvider;

return [
    'xepozz/unique-id' => [
        'providers' => [
            CurrentUserProvider::class,
            CookieProvider::class,
            SessionProvider::class,
            HeaderProvider::class,
        ],
        'generators' => [
            UuidGenerator::class,
        ],
    ],
    //'yiisoft/yii-debug' => [
    //    'collectors' => [
    //        IDCollector::class,
    //    ],
    //    'trackedServices' => [
    //        GuestIdentityFactoryInterface::class => [GuestIdentityFactoryInterfaceProxy::class, IDCollector::class],
    //    ],
    //],
];