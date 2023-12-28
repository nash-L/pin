<?php

namespace Nash\Pin\Server;

use Hyperf\Context\Context;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\HttpServer\Contract\RequestInterface;
use Nash\Pin\Core\Container;
use Nash\Pin\Core\StaticObjectTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    use StaticObjectTrait;

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __call(string $name, array $arguments)
    {
        $result = Container::instance()->get(RequestInterface::class)->{$name}(...$arguments);
        if ($result instanceof ServerRequestInterface) {
            Context::set(ServerRequestInterface::class, $result);
            return $this;
        }
        return $result;
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getProtocolVersion(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $version
     * @return MessageInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withProtocolVersion(string $version): MessageInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return array|string[][]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getHeaders(): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function hasHeader(string $name): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @return array|string[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getHeader(string $name): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getHeaderLine(string $name): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @param $value
     * @return MessageInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withHeader(string $name, $value): MessageInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @param $value
     * @return MessageInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withAddedHeader(string $name, $value): MessageInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @return MessageInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withoutHeader(string $name): MessageInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return StreamInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getBody(): StreamInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param StreamInterface $body
     * @return MessageInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getPathInfo(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $method
     * @return \Psr\Http\Message\RequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withMethod(string $method): \Psr\Http\Message\RequestInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param UriInterface $uri
     * @param bool $preserveHost
     * @return \Psr\Http\Message\RequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withUri(UriInterface $uri, bool $preserveHost = false): \Psr\Http\Message\RequestInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param array $keys
     * @param array|null $default
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function inputs(array $keys, array $default = null): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getMethod(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function all(): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function query(?string $key = null, mixed $default = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function post(?string $key = null, mixed $default = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param array|string $keys
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function has(array|string $keys): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return string|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function header(string $key, ?string $default = null): ?string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function route(string $key, mixed $default = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param ...$patterns
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function is(...$patterns): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function decodedPath(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getRequestUri(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function hasCookie(string $key): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function hasFile(string $key): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return UriInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUri(): UriInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param array $keys
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function hasInput(array $keys): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function url(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function fullUrl(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return string|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getQueryString(): ?string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $qs
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function normalizeQueryString(string $qs): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function server(string $key, mixed $default = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $method
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function isMethod(string $method): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return null|UploadedFile|UploadedFile[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function file(string $key, mixed $default = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getRequestTarget(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $requestTarget
     * @return \Psr\Http\Message\RequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withRequestTarget(string $requestTarget): \Psr\Http\Message\RequestInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getServerParams(): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCookieParams(): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param array $cookies
     * @return ServerRequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getQueryParams(): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param array $query
     * @return ServerRequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withQueryParams(array $query): ServerRequestInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUploadedFiles(): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param array $uploadedFiles
     * @return ServerRequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getParsedBody(): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param $data
     * @return ServerRequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAttributes(): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAttribute(string $name, $default = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @param $value
     * @return ServerRequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @return ServerRequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
