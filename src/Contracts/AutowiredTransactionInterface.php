<?php

namespace Nacosvel\DatabaseManager\Contracts;

use Nacosvel\DatabaseManager\Middleware\RoosterServerMiddleware;
use Nacosvel\Feign\Annotation\FeignClient;
use Nacosvel\Feign\Annotation\RequestMiddleware;
use Nacosvel\Feign\Annotation\RequestPostMapping;
use Nacosvel\Feign\Support\Service;

#[FeignClient(
    name: 'rooter-server',
    path: '/rooster/transaction'
)]
#[RequestMiddleware(value: RoosterServerMiddleware::class)]
interface AutowiredTransactionInterface
{
    #[RequestPostMapping(path: '/create')]
    public function create(array $data = []): Service;

    #[RequestPostMapping(path: '/detail')]
    public function detail(array $data = []): Service;

    #[RequestPostMapping(path: '/update')]
    public function update(array $queries = [], array $data = []): Service;

}
