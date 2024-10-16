<?php

namespace Nacosvel\DatabaseManager\Contracts;

use Nacosvel\DatabaseManager\Middleware\RoosterServerMiddleware;
use Nacosvel\Feign\Annotation\FeignClient;
use Nacosvel\Feign\Annotation\RequestMiddleware;
use Nacosvel\Feign\Annotation\RequestPostMapping;
use Nacosvel\Feign\Support\Service;

#[FeignClient(
    name: 'rooter-server',
    path: '/rooster-server/transaction-coordinator'
)]
#[RequestMiddleware(value: RoosterServerMiddleware::class)]
interface AutowiredTransactionCoordinatorInterface
{
    #[RequestPostMapping(path: '/commit')]
    public function commit(array $data = []): Service;

    #[RequestPostMapping(path: '/rollback')]
    public function rollback(array $data = []): Service;

}
