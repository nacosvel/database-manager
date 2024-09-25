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
