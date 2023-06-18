<?php

declare(strict_types=1);

namespace Xepozz\UniqueID\Tests;

use Closure;
use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Xepozz\UniqueID\Provider\CookieProvider;
use Xepozz\UniqueID\Provider\CurrentUserProvider;
use Xepozz\UniqueID\Provider\HeaderProvider;
use Xepozz\UniqueID\Provider\SessionProvider;
use Xepozz\UniqueID\SetUniqueUserIDMiddleware;
use Xepozz\UniqueID\Tests\Support\StubGenerator;
use Xepozz\UniqueID\Tests\Support\StubIdentity;
use Xepozz\UniqueID\Tests\Support\StubSession;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Cookies\Cookie;
use Yiisoft\Test\Support\EventDispatcher\SimpleEventDispatcher;
use Yiisoft\User\CurrentUser;
use Yiisoft\User\Guest\GuestIdentity;

final class SetUniqueUserIDMiddlewareTest extends TestCase
{
    public function testCurrentUserProvider()
    {
        $identity = new StubIdentity($userId = '111');
        $currentUser = new CurrentUser(
            $this->createIdentityRepository($identity),
            new SimpleEventDispatcher(),
        );
        $currentUser->login($identity);

        $providers = [
            new HeaderProvider($headerName1 = 'X-Unique-ID1'),
            new HeaderProvider($headerName2 = 'X-Unique-ID2'),
            new CookieProvider($cookieName = 'X-Unique-ID3'),
            new CurrentUserProvider($currentUser),
        ];

        $generators = [new StubGenerator('222'), new StubGenerator('333')];

        $request = new ServerRequest();
        $middleware = $this->createMiddleware(
            $providers,
            $generators,
            $currentUser,
        );
        $handler = $this->createHandler();

        $response = $middleware->process($request, $handler);

        $this->assertEquals($userId, $response->getHeaderLine($headerName1));
        $this->assertEquals($userId, $response->getHeaderLine($headerName2));

        $cookie = Cookie::fromCookieString($response->getHeaderLine('Set-Cookie'));
        $this->assertEquals($cookieName, $cookie->getName());
        $this->assertEquals($userId, $cookie->getValue());
    }

    public function testMultipleProviders()
    {
        $providers = [
            new HeaderProvider($headerName1 = 'X-Unique-ID1'),
            new HeaderProvider($headerName2 = 'X-Unique-ID2'),
            new CookieProvider($cookieName = 'X-Unique-ID3'),
        ];
        $generators = [new StubGenerator(null), new StubGenerator($userId = '444')];

        $request = new ServerRequest();
        $middleware = $this->createMiddleware(
            $providers,
            $generators,
        );
        $handler = $this->createHandler();

        $response = $middleware->process($request, $handler);

        $this->assertEquals($userId, $response->getHeaderLine($headerName1));
        $this->assertEquals($userId, $response->getHeaderLine($headerName2));

        $cookie = Cookie::fromCookieString($response->getHeaderLine('Set-Cookie'));
        $this->assertEquals($cookieName, $cookie->getName());
        $this->assertEquals($userId, $cookie->getValue());
    }

    #[DataProvider('dataGenerators')]
    #[DataProvider('dataProviders')]
    public function testGeneratorsAndProviders(
        array $providers,
        array $generators,
        ServerRequestInterface $request,
        Closure $userIdFetcher,
        string $expectedUserId
    ) {
        $middleware = $this->createMiddleware(
            $providers,
            $generators,
        );
        $handler = $this->createHandler();

        $response = $middleware->process($request, $handler);

        $actualUserId = $userIdFetcher($response);
        $this->assertEquals($expectedUserId, $actualUserId);
    }

    public static function dataProviders(): iterable
    {
        yield 'only header provider' => [
            [new HeaderProvider($headerName = 'X-Unique-ID')],
            [],
            new ServerRequest(headers: [$headerName => $userId = '123']),
            fn (ResponseInterface $response) => $response->getHeaderLine($headerName),
            $userId,
        ];
        yield 'cookie provider' => [
            [new CookieProvider($cookieNmae = 'X-Unique-ID')],
            [],
            new ServerRequest(cookieParams: [$cookieNmae => $userId = '333']),
            fn (ResponseInterface $response) => Cookie::fromCookieString(
                $response->getHeaderLine('Set-Cookie')
            )->getValue(),
            $userId,
        ];
        yield 'session provider' => [
            [new SessionProvider($session = new StubSession([$sessionKey = 'X-Unique-ID' => $userId = '333']))],
            [],
            new ServerRequest(),
            fn (ResponseInterface $response) => $session->get($sessionKey),
            $userId,
        ];
    }

    public static function dataGenerators(): iterable
    {
        yield 'stub + header generator' => [
            [new HeaderProvider($headerName = 'X-Unique-ID')],
            [new StubGenerator($userId = '444')],
            new ServerRequest(),
            fn (ResponseInterface $response) => $response->getHeaderLine($headerName),
            $userId,
        ];
        yield 'stub + cookie provider' => [
            [new CookieProvider($cookieNmae = 'X-Unique-ID')],
            [new StubGenerator($userId = '444')],
            new ServerRequest(cookieParams: [$cookieNmae => $userId]),
            fn (ResponseInterface $response) => Cookie::fromCookieString(
                $response->getHeaderLine('Set-Cookie')
            )->getValue(),
            $userId,
        ];
        yield 'stub + session provider' => [
            [new SessionProvider($session = new StubSession([$sessionKey = 'X-Unique-ID' => $userId = '333']))],
            [new StubGenerator($userId)],
            new ServerRequest(),
            fn (ResponseInterface $response) => $session->get($sessionKey),
            $userId,
        ];
    }

    private function createMiddleware(
        array $providers = [],
        array $generators = [],
        CurrentUser $currentUser = null
    ): SetUniqueUserIDMiddleware {
        return new SetUniqueUserIDMiddleware(
            $currentUser ?? new CurrentUser(
            $this->createIdentityRepository(new GuestIdentity()),
            new SimpleEventDispatcher(),
        ),
            $providers,
            $generators,
        );
    }

    private function createHandler(): RequestHandlerInterface
    {
        return new class() implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };
    }

    private function createIdentityRepository(?IdentityInterface $identity)
    {
        return new class($identity) implements IdentityRepositoryInterface {
            public function __construct(
                private readonly ?IdentityInterface $identity,
            ) {
            }

            public function findIdentity(string $id): ?IdentityInterface
            {
                return $this->identity;
            }
        };
    }
}
