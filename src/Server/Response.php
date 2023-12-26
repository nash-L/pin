<?php

namespace Nash\Pin\Server;

use Hyperf\Context\ResponseContext;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Nash\Pin\Core\Container;
use Nash\Pin\Core\StaticObjectTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use Swow\Psr7\Message\ResponsePlusInterface;

class Response implements ResponseInterface, PsrResponseInterface
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
        if (in_array($name, ['withCookie', 'withHeader', 'withProtocolVersion', 'withStatus', 'withAddedHeader', 'withoutHeader', 'withBody'])) {
            $result = ResponseContext::get()->{$name}(...$arguments);
        } else {
            $result = Container::instance()->get(ResponseInterface::class)->{$name}(...$arguments);
        }
        if ($result instanceof ResponsePlusInterface) {
            ResponseContext::set($result);
            return $this;
        }
        return $result;
    }

    /**
     * @param $data
     * @return PsrResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function json($data): PsrResponseInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param $data
     * @param string $root
     * @param string $charset
     * @return PsrResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function xml($data, string $root = 'root', string $charset = 'utf-8'): PsrResponseInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param $data
     * @param string $charset
     * @return PsrResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function raw($data, string $charset = 'utf-8'): PsrResponseInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $html
     * @param string $charset
     * @return PsrResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function html(string $html, string $charset = 'utf-8'): PsrResponseInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $toUrl
     * @param int $status
     * @param string $schema
     * @return PsrResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function redirect(string $toUrl, int $status = 302, string $schema = 'http'): PsrResponseInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $file
     * @param string $name
     * @return PsrResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function download(string $file, string $name = ''): PsrResponseInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $data
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function write(string $data): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param Cookie $cookie
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withCookie(Cookie $cookie): self
    {
        return $this->__call(__FUNCTION__, func_get_args());
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
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withProtocolVersion(string $version): self
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return array
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
     * @return array
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
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withHeader(string $name, $value): self
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @param $value
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withAddedHeader(string $name, $value): self
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withoutHeader(string $name): self
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
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withBody(StreamInterface $body): self
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getStatusCode(): int
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function withStatus(int $code, string $reasonPhrase = ''): self
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getReasonPhrase(): string
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
