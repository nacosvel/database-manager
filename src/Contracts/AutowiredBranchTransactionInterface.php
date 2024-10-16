<?php

namespace Nacosvel\DatabaseManager\Contracts;

use Nacosvel\DatabaseManager\Middleware\RoosterServerMiddleware;
use Nacosvel\Feign\Annotation\FeignClient;
use Nacosvel\Feign\Annotation\RequestMiddleware;
use Nacosvel\Feign\Annotation\RequestPostMapping;
use Nacosvel\Feign\Support\Service;

#[FeignClient(
    name: 'rooter-server',
    path: '/rooster/branch'
)]
#[RequestMiddleware(value: RoosterServerMiddleware::class)]
interface AutowiredBranchTransactionInterface
{
    #[RequestPostMapping(path: '/create')]
    public function create(array $data = []): Service;

}
