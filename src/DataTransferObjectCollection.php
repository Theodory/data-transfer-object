<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use Iterator;
use Countable;
use ArrayAccess;

abstract class DataTransferObjectCollection implements
    ArrayAccess,
    Iterator,
    Countable
{
    /** @var array */
    protected $collection;

    /** @var int */
    protected $position = 0;

    public function __construct(array $collection = [])
    {
        $this->collection = $collection;
    }

    public function current()
    {
        return $this->collection[$this->position];
    }

    public function offsetGet($offset)
    {
        return $this->collection[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->collection);
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    public function next()
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return array_key_exists($this->position, $this->collection);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function toArray(): array
    {
        $collection = $this->collection;

        foreach ($collection as $key => $item) {
            if (
                ! $item instanceof DataTransferObject
                && ! $item instanceof DataTransferObjectCollection
            ) {
                continue;
            }

            $collection[$key] = $item->toArray();
        }

        return $collection;
    }

    public function items(): array
    {
        return $this->collection;
    }

    public function count(): int
    {
        return count($this->collection);
    }
}
