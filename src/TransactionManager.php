<?php

namespace Nacosvel\DatabaseManager;

use Nacosvel\Contracts\DatabaseManager\TransactionManagerInterface;
use Nacosvel\Contracts\Rooster\Action;
use Nacosvel\Contracts\Rooster\Code;
use Nacosvel\Contracts\Rooster\Status;
use Nacosvel\DatabaseManager\Contracts\AutowiredBranchTransactionInterface;
use Nacosvel\DatabaseManager\Contracts\AutowiredTransactionCoordinatorInterface;
use Nacosvel\DatabaseManager\Contracts\AutowiredTransactionInterface;
use Nacosvel\DatabaseManager\Contracts\ChainInterface;
use Nacosvel\DatabaseManager\Facades\DB;
use Nacosvel\DatabaseManager\Support\Chain;
use Nacosvel\Feign\Annotation\Autowired;
use Nacosvel\Feign\Contracts\AutowiredInterface;
use Symfony\Component\Uid\Ulid;

class TransactionManager implements TransactionManagerInterface, AutowiredInterface
{
    protected ChainInterface $transactionChain;
    protected bool           $internalInvocation  = false;
    protected array          $queries             = [];
    protected array          $transactionRollback = [];
    protected ?string        $requestID           = null;
    protected ?string        $branchId            = null;

    #[Autowired]
    protected AutowiredInterface|AutowiredTransactionCoordinatorInterface $coordinator;

    #[Autowired]
    protected AutowiredInterface|AutowiredTransactionInterface $transaction;

    #[Autowired]
    protected AutowiredInterface|AutowiredBranchTransactionInterface $branch;

    public function __construct()
    {
        $this->transactionChain = new Chain();
    }

    /**
     * Creates a ULID
     *
     * @return string
     */
    public function ulid(): string
    {
        return Ulid::generate();
    }

    /**
     * Start a new database transaction.
     *
     * @return string
     */
    public function beginTransaction(): string
    {
        $this->transactionChain->push($xId = $this->ulid());

        if ($this->transactionChain->count() == 1) {
            $response = $this->transaction->create(data: [
                'transaction_id'       => $xId,
                'transaction_rollback' => json_encode([]),
                'transaction_recover'  => json_encode([]),
                'status'               => Action::fromValue(Action::ACTION_START),
            ]);
        }
        $this->stmtBeginTransaction();

        return $this->transactionChain->toString();
    }

    /**
     * Rollback the active database transaction.
     *
     * @return bool
     */
    public function commit(): bool
    {
        $this->stmtRollback();

        if ($this->transactionChain->count() > 1) {
            $this->transactionChain->pop();
            return true;
        }

        $this->flush(Status::fromValue(Status::COMMIT));

        $response = $this->coordinator->commit(data: [
            'transaction_id'       => $this->transactionChain->toString(),
            'transaction_rollback' => json_encode($this->transactionRollback),
        ]);

        $this->reset();

        return $response->get('code') === Code::SUCCESS;
    }

    /**
     * Rollback the active database transaction.
     *
     * @return bool
     */
    public function rollBack(): bool
    {
        $this->stmtRollback();

        if ($this->transactionChain->count() > 1) {
            $xId                         = $this->transactionChain->toString();
            $this->transactionRollback   = array_filter($this->transactionRollback, function ($txId) use ($xId) {
                return str_starts_with($txId, $xId) === false;
            });
            $this->transactionRollback[] = $xId;
            $this->transactionChain->pop();
            return true;
        }

        $this->flush(Status::fromValue(Status::ROLLBACK));

        $response = $this->coordinator->rollback(data: [
            'transaction_id'       => $this->transactionChain->toString(),
            'transaction_rollback' => json_encode($this->transactionRollback),
        ]);

        $this->reset();

        return $response->get('code') === Code::SUCCESS;
    }

    public function middlewareBeginTransaction(string $xId): void
    {
        $this->transactionChain = new Chain($xId);
    }

    public function middlewareRollback(): void
    {
        $this->stmtRollback();

        $this->flush(Status::fromValue(Status::COMMIT));

        if ($this->transactionRollback) {
            $response = $this->transaction->detail(data: [
                'transaction_id' => $this->transactionChain->reset(),
            ]);

            $data                = $response->get('data') ?? [];
            $transactionRollback = array_merge($data['transaction_rollback'] ?? [], $this->transactionRollback);
            $transactionRollback = array_unique($transactionRollback);

            $this->transaction->update(queries: [
                'transaction_id' => $this->transactionChain->reset(),
            ], data: [
                'transaction_rollback' => json_encode($transactionRollback),
            ]);
        }

        $this->reset();
    }

    public function flush(string $status): void
    {
        if (count($this->queries) == 0) {
            return;
        }

        $queries   = [];
        $xId       = $this->transactionChain->reset();
        $requestID = $this->requestID ?? $xId;
        foreach ($this->queries as $query) {
            $queries[] = [
                'transaction_id'  => $xId,
                'request_id'      => $requestID,
                'chain_id'        => $query['xid'] ?? '',
                'transact_status' => $status,
                'sql'             => $query['sql'] ?? '',
                'result'          => $query['result'] ?? '',
                'check_result'    => $query['check_result'] ?? '',
                'connection'      => $query['connection'] ?? '',
            ];
        }

        $response = $this->branch->create(data: [
            'queries' => json_encode($queries),
        ]);
    }

    protected function reset(): void
    {
        $this->transactionChain = new Chain();
        $this->queries          = [];
        $this->requestID        = null;
    }

    protected function stmtBeginTransaction(): void
    {
        $this->internalInvocation = true;
        DB::beginTransaction();
        $this->internalInvocation = false;
    }

    protected function stmtRollback(): void
    {
        $this->internalInvocation = true;
        DB::rollBack();
        $this->internalInvocation = false;
    }

}
