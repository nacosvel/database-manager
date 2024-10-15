<?php

namespace Nacosvel\DatabaseManager\Contracts;

interface ChainInterface
{
    /**
     * Check whether array contains more entries
     *
     * @return bool
     */
    public function valid(): bool;

    /**
     * Return current array entry
     *
     * @return mixed The current array entry.
     */
    public function current(): mixed;

    /**
     * Move to next entry
     *
     * @link https://php.net/manual/en/arrayiterator.next.php
     * @return void
     */
    public function next(): void;

    /**
     * Rewind array back to the start
     *
     * @return void
     */
    public function rewind(): void;

    /**
     * Count elements
     *
     * @return int<0,max> The number of elements or public properties in the associated array or object, respectively.
     */
    public function count(): int;

    /**
     * Check if offset exists
     *
     * @param string $key The offset being checked.
     *
     * @return bool true if the offset exists, otherwise false
     */
    public function has(string $key): bool;

    /**
     * Get array copy
     *
     * @return array A copy of the array, or array of public properties if ArrayIterator refers to an object.
     */
    public function toArray(): array;

    /**
     * Split a string by a string
     *
     * @param string $separator
     *
     * @return array
     */
    public function explode(string $separator = ','): array;

    /**
     * Join array elements with a string
     *
     * @param string $separator
     *
     * @return string
     */
    public function implode(string $separator = ','): string;

    /**
     * Alias:
     * {@see implode}
     *
     * @param string $separator
     *
     * @return string
     */
    public function toString(string $separator = ','): string;

    /**
     * Push elements onto the end of array
     *
     * @param mixed $value The value to append.
     *
     * @return static
     */
    public function push(mixed $value): static;

    /**
     * Pop the element off the end of array
     *
     * @return mixed|null the last value of array. If array is empty null will be returned.
     */
    public function pop(): mixed;

    /**
     * Set the internal pointer of an array to its first element
     *
     * @return mixed
     */
    public function reset(): mixed;

    /**
     * Set the internal pointer of an array to its last element
     *
     * @return mixed
     */
    public function end(): mixed;

    /**
     * Remove all items.
     *
     * @return static
     */
    public function flush(): static;

}
