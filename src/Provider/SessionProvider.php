<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Provider;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Xepozz\UniqueID\IdProviderInterface;
use Yiisoft\Session\SessionInterface;

final class SessionProvider implements IdProviderInterface
{
    private readonly SessionInterface $session;

    public function __construct(
        SessionInterface $session = null,
        private readonly string $attribute = 'user_id',
    ) {
        if ($session === null) {
            throw new \RuntimeException(
                sprintf(
                    'Session is not configured. Please install yiisoft/session package or configure session explicitly.',
                )
            );
        }
        $this->session = $session;
    }

    public function get(ServerRequestInterface $request): string
    {
        if (!$this->session->isActive()) {
            throw new \RuntimeException('Session is not active');
        }
        if (!$this->session->has($this->attribute)) {
            throw new \RuntimeException(sprintf('Session attribute "%s" not found', $this->attribute));
        }

        $value = $this->session->get($this->attribute);
        if (!is_string($value)) {
            throw new \RuntimeException(sprintf('Session attribute "%s" is not a string', $this->attribute));
        }

        return $value;
    }

    public function has(ServerRequestInterface $request): bool
    {
        if (!$this->session->isActive()) {
            return false;
        }
        if (!$this->session->has($this->attribute)) {
            return false;
        }

        $value = $this->session->get($this->attribute);

        return is_string($value);
    }

    public function wrap(ResponseInterface $response, string $uniqueUserID): ResponseInterface
    {
        $this->session->set($this->attribute, $uniqueUserID);

        return $response;
    }
}