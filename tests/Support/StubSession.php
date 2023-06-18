<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Tests\Support;

use Yiisoft\Session\SessionInterface;

class StubSession implements SessionInterface
{
    public function __construct(private array $values)
    {
    }

    public function get(string $key, $default = null): string
    {
        return $this->values[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->values[$key] = $value;
    }

    public function close(): void
    {
    }

    public function open(): void
    {
    }

    public function isActive(): bool
    {
        return true;
    }

    public function getId(): ?string
    {
    }

    public function setId(string $sessionId): void
    {
    }

    public function regenerateId(): void
    {
    }

    public function discard(): void
    {
    }

    public function getName(): string
    {
    }

    public function all(): array
    {
    }

    public function remove(string $key): void
    {
    }

    public function has(string $key): bool
    {
        return isset($this->values[$key]);
    }

    public function pull(string $key, $default = null)
    {
    }

    public function clear(): void
    {
    }

    public function destroy(): void
    {
    }

    public function getCookieParameters(): array
    {
    }
}