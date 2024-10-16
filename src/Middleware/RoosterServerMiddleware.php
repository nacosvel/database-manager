<?php

namespace Nacosvel\DatabaseManager\Middleware;

use GuzzleHttp\Promise\PromiseInterface;
use Nacosvel\Feign\Middleware\RequestMiddleware;
use Psr\Http\Message\RequestInterface;
use function Nacosvel\Container\Interop\application;

class RoosterServerMiddleware extends RequestMiddleware
{
    #[\Override]
    public function request(RequestInterface $request, array $options): RequestInterface|PromiseInterface
    {
        return $request
            ->withAddedHeader('tx-service-name', application('tx_service_name'))
            ->withAddedHeader('tx-server-group', application('tx_server_group'));
    }

}
