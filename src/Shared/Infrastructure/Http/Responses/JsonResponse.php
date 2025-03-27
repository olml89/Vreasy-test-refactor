<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use Tempest\Http\Status;
use Tempest\Router\ContentType;
use Tempest\Router\Cookie\Cookie;
use Tempest\Router\Header;
use Tempest\Router\Response;

abstract class JsonResponse implements Response
{
    protected(set) Status $status = Status::OK;
    protected(set) ?array $body = null;

    /** @var Header[] */
    protected(set) array $headers = [];

    public function __construct(Status $status)
    {
        $this
            ->setContentType(ContentType::JSON)
            ->setStatus($status);
    }

    public function getHeader(string $name): ?Header
    {
        return $this->headers[$name] ?? null;
    }

    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] ??= new Header($key);
        $this->headers[$key]->add($value);

        return $this;
    }

    public function removeHeader(string $key): self
    {
        unset($this->headers[$key]);

        return $this;
    }

    public function addSession(string $name, mixed $value): self
    {
        return $this;
    }

    public function removeSession(string $name): self
    {
        return $this;
    }

    public function destroySession(): self
    {
        return $this;
    }

    public function addCookie(Cookie $cookie): self
    {
        return $this;
    }

    public function removeCookie(string $key): self
    {
        return $this;
    }

    public function flash(string $key, mixed $value): self
    {
        return $this;
    }

    public function setContentType(ContentType $contentType): self
    {
        $this
            ->removeHeader(ContentType::HEADER)
            ->addHeader(ContentType::HEADER, $contentType->value);

        return $this;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }
}