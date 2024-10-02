<?php

namespace Nacosvel\DatabaseManager\Facades;

use Nacosvel\Contracts\DatabaseManager\DatabaseManagerInterface;
use Nacosvel\DatabaseManager\DatabaseManager;
use Nacosvel\Facades\Facade;
use RuntimeException;
use function Nacosvel\Container\Interop\application;

/**
 * @method static void beginTransaction()
 * @method static void commit()
 * @method static void rollBack()
 * @method static void unprepared(string $query)
 * @method static mixed connection(string $name = null)
 * @method static mixed getDatabaseConfig(string $option = null)
 * @method static void startTransactionXa(string $xid)
 * @method static void endTransactionXa(string $xid)
 * @method static void prepareXa(string $xid)
 * @method static void commitXa(string $xid)
 * @method static void rollbackXa(string $xid)
 *
 * @see DatabaseManager
 */
class DB extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        if (false === application()->getContainer()->has(DatabaseManagerInterface::class)) {
            application()->bind(DatabaseManagerInterface::class, function () {
                if (application()->getContainer()->has('db')) {
                    return new DatabaseManager(application('db'));
                }
                throw new RuntimeException(sprintf('Please bind the instance of %s to the container.', DatabaseManagerInterface::class));
            });
        }
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
