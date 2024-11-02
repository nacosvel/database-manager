<?php

namespace Nacosvel\DatabaseManager;

use Nacosvel\Contracts\DatabaseManager\DatabaseManagerInterface;

class TransactionManager implements DatabaseManagerInterface
{
    public function __construct(
        protected mixed $manager,
        protected mixed $transaction,
    )
    {
        //
    }

    /**
     * Get a database connection instance.
     *
     * @param string|null $name
     *
     * @return mixed
     */
    public function connection(string $name = null): mixed
    {
        return call_user_func($this->transaction, call_user_func([$this->manager, 'connection'], $name));
    }

    /**
     * Set the default database connection for the callback execution.
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return mixed
     */
    public function usingConnection(string $name, callable $callback): mixed
    {
        return call_user_func([$this->manager, 'usingConnection'], $name, $callback);
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }

}
