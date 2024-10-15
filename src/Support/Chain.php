<?php

namespace Nacosvel\DatabaseManager\Support;

use ArrayIterator;
use Nacosvel\DatabaseManager\Contracts\ChainInterface;

class Chain implements ChainInterface
{
    protected ArrayIterator $chain;

    public function __construct(object|array|string $chains = [])
    {
        if (is_string($chains)) {
            $chains = explode(',', $chains);
        }
        $this->chain = new ArrayIterator($chains);
    }

    /**
     * Check whether array contains more entries
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->chain->valid();
    }

    /**
     * Return current array entry
     *
     * @return mixed The current array entry.
     */
    public function current(): mixed
    {
        return $this->chain->current();
    }

    /**
     * Move to next entry
     *
     * @link https://php.net/manual/en/arrayiterator.next.php
     * @return void
     */
    public function next(): void
    {
        $this->chain->next();
    }

    /**
     * Rewind array back to the start
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->chain->rewind();
    }

    /**
     * Count elements
     *
     * @return int<0,max> The number of elements or public properties in the associated array or object, respectively.
     */
    public function count(): int
    {
        return $this->chain->count();
    }

    /**
     * Check if offset exists
     *
     * @param string $key The offset being checked.
     *
     * @return bool true if the offset exists, otherwise false
     */
    public function has(string $key): bool
    {
        return $this->chain->offsetExists($key);
    }

    /**
     * Get array copy
     *
     * @return array A copy of the array, or array of public properties if ArrayIterator refers to an object.
     */
    public function toArray(): array
    {
        return $this->chain->getArrayCopy();
    }

    /**
     * Split a string by a string
     *
     * @param string $separator
     *
     * @return array
     */
    public function explode(string $separator = ','): array
    {
        return explode($separator, $this->toArray());
    }

    /**
     * Join array elements with a string
     *
     * @param string $separator
     *
     * @return string
     */
    public function implode(string $separator = ','): string
    {
        return implode($separator, $this->toArray());
    }

    /**
     * Alias:
     * {@see implode}
     *
     * @param string $separator
     *
     * @return string
     */
    public function toString(string $separator = ','): string
    {
        return $this->implode($separator);
    }

    /**
     * Push elements onto the end of array
     *
     * @param mixed $value The value to append.
     *
     * @return static
     */
    public function push(mixed $value): static
    {
        return $this->chain->append($value) ?? $this;
    }

    /**
     * Pop the element off the end of array
     *
     * @return mixed|null the last value of array. If array is empty null will be returned.
     */
    public function pop(): mixed
    {
        $chains = $this->toArray();
        $chain  = fn($pop) => function ($chains) use ($pop) {
            $this->chain = new ArrayIterator($chains);
            return $pop;
        };
        return $chain(array_pop($chains))($chains);
    }

    /**
     * Set the internal pointer of an array to its first element
     *
     * @return mixed
     */
    public function reset(): mixed
    {
        return $this->chain->rewind() ?? $this->chain->current();
    }

    /**
     * Set the internal pointer of an array to its last element
     *
     * @return mixed
     */
    public function end(): mixed
    {
        return $this->chain->seek($this->chain->count()) ?? $this->chain->current();
    }

    /**
     * Remove all items.
     *
     * @return static
     */
    public function flush(): static
    {
        $this->chain = new ArrayIterator([]);
        return $this;
    }

}
