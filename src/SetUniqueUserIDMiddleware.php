<?php

declare(strict_types=1);

namespace Xepozz\UniqueID;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\User\CurrentUser;

final class SetUniqueUserIDMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly CurrentUser $currentUser,
        /**
         * @var array<IdProviderInterface>
         */
        private readonly array $providers,
        /**
         * @var array<IdGeneratorInterface>
         */
        private readonly array $generators,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uniqueUserID = $this->currentUser->getId() ?? $this->getOrGenerateUniqueID($request);

        $response = $handler->handle($request);

        $id = $this->currentUser->getId() ?? $uniqueUserID;

        if ($id !== null) {
            return $this->wrapResponse($response, $id);
        }

        return $response;
    }

    private function wrapResponse(ResponseInterface $response, string $id): ResponseInterface
    {
        foreach ($this->providers as $provider) {
            $response = $provider->wrap($response, $id);
        }
        return $response;
    }

    private function getOrGenerateUniqueID(ServerRequestInterface $request): ?string
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($request)) {
                return $provider->get($request);
            }
        }
        foreach ($this->generators as $generator) {
            $uniqueUserID = $generator->generate($request);
            if ($uniqueUserID !== null) {
                return $uniqueUserID;
            }
        }
        return null;
    }
}
