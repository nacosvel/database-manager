<?php

namespace Nacosvel\DatabaseManager\Facades;

use Nacosvel\Contracts\DatabaseManager\DatabaseManagerInterface;
use Nacosvel\DatabaseManager\DatabaseManager;
use Nacosvel\Facades\Facade;
use PDO;
use function Nacosvel\Container\Interop\application;

/**
 * @method static mixed connection(string $name = null) Get a database connection instance.
 * @method static mixed usingConnection(string $name, callable $callback) Set the default database connection for the callback execution.
 *
 * @method static null|string getName() Get the database connection name.
 * @method static mixed getConnectionConfiguration(string $option = null, mixed $default = null) Get the configuration for a connection.
 * @method static PDO getPdo() Get the current PDO connection.
 * @method static void unprepared(string $query) Run a raw, unprepared query against the PDO connection.
 * @method static void beginTransaction() Start a new database transaction.
 * @method static void commit() Commit the active database transaction.
 * @method static void rollBack() Rollback the active database transaction.
 * @method static void xaBeginTransaction(string $xid) Starts a distributed XA transaction with the given XID.
 * @method static void xaPrepare(string $xid) Prepares the distributed XA transaction with the given XID for commit.
 * @method static void xaCommit(string $xid) Commits the distributed XA transaction with the given XID.
 * @method static void xaRollBack(string $xid) Rolls back the distributed XA transaction with the given XID.
 * @method static void xaRecover(string $xid) Recovers the distributed XA transaction with the given XID.
 *
 * @see DatabaseManager
 */
class DB extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return DatabaseManagerInterface::class;
    }

    /**
     * Get a resolved facade instance.
     *
     * @return DatabaseManagerInterface
     */
    protected static function getFacadeInstance(): DatabaseManagerInterface
    {
        return application(DatabaseManagerInterface::class);
    }

}
