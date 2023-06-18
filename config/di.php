<?php

declare(strict_types=1);

use Xepozz\UniqueID\SetUniqueUserIDMiddleware;
use Yiisoft\Definitions\DynamicReferencesArray;

/**
 * @var array $params
 */
return [
    SetUniqueUserIDMiddleware::class => [
        '__construct()' => [
            'providers' => DynamicReferencesArray::from($params['xepozz/unique-id']['providers']),
            'generators' => DynamicReferencesArray::from($params['xepozz/unique-id']['generators']),
        ],
    ],
];