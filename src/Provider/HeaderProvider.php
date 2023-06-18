<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Provider;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Xepozz\UniqueID\IdProviderInterface;

final class HeaderProvider implements IdProviderInterface
{
    public function __construct(
        private readonly string $headerName = 'X-User-ID',
    ) {
    }

    public function get(ServerRequestInterface $request): string
    {
        if (!$request->hasHeader($this->headerName)) {
            throw new \RuntimeException(sprintf('Header "%s" not found', $this->headerName));
        }
        $value = $request->getHeaderLine($this->headerName);
        if (empty($value)) {
            throw new \RuntimeException(sprintf('Header "%s" is empty', $this->headerName));
        }
        return $value;
    }

    public function has(ServerRequestInterface $request): bool
    {
        if (!$request->hasHeader($this->headerName)) {
            return false;
        }
        $value = $request->getHeaderLine($this->headerName);
        return !empty($value);
    }

    public function wrap(ResponseInterface $response, string $uniqueUserID): ResponseInterface
    {
        return $response->withHeader($this->headerName, $uniqueUserID);
    }
}