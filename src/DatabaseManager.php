<?php

namespace Nacosvel\DatabaseManager;

use Nacosvel\Contracts\DatabaseManager\DatabaseManagerInterface;
use RuntimeException;

class DatabaseManager implements DatabaseManagerInterface
{
    protected mixed $manager;

    public function __construct(mixed $manager)
    {
        $this->setManager($manager);
    }

    /**
     * @return mixed
     */
    public function getManager(): mixed
    {
        return $this->manager;
    }

    /**
     * @param mixed $manager
     *
     * @return static
     */
    public function setManager(mixed $manager): static
    {
        if (is_null($manager) || is_object($manager) === false) {
            throw new RuntimeException(sprintf(
                'The $manager parameter in the %s method must be an object, %s given.',
                __METHOD__,
                $manager ?: 'null'
            ));
        }
        $this->manager = $manager;
        return $this;
    }

    /**
     * Start a new database transaction.
     *
     * @return void
     */
    public function beginTransaction(): void
    {
        call_user_func([$this->getManager(), __FUNCTION__]);
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     */
    public function commit(): void
    {
        call_user_func([$this->getManager(), __FUNCTION__]);
    }

    /**
     * Rollback the active database transaction.
     *
     * @return void
     */
    public function rollBack(): void
    {
        call_user_func([$this->getManager(), __FUNCTION__]);
    }

    /**
     * Run a raw, unprepared query against the PDO connection.
     *
     * @param string $query
     *
     * @return void
     */
    public function unprepared(string $query): void
    {
        call_user_func([$this->getManager(), __FUNCTION__], $query);
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
        return call_user_func_array([$this->getManager(), $method], $parameters);
    }

}
