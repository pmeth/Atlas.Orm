<?php
namespace Atlas\Orm\Mapper;

use ArrayAccess;
use ArrayIterator;
use Atlas\Orm\Exception;
use Countable;
use IteratorAggregate;

class RecordSet implements ArrayAccess, Countable, IteratorAggregate
{
    private $records = [];

    private $recordClass;

    public function __construct(array $records = [])
    {
        $this->recordClass = substr(get_class($this), 0, -3);
        foreach ($records as $key => $record) {
            $this->offsetSet($key, $record);
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->records[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->records[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (! is_object($value)) {
            throw Exception::invalidType($this->recordClass, gettype($value));
        }

        if (! $value instanceof $this->recordClass) {
            throw Exception::invalidType($this->recordClass, $value);
        }

        if ($offset === null) {
            $this->records[] = $value;
            return;
        }

        $this->records[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->records[$offset]);
    }

    public function count()
    {
        return count($this->records);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->records);
    }

    public function isEmpty()
    {
        return ! $this->records;
    }

    public function getArrayCopy()
    {
        $array = [];
        foreach ($this as $key => $record) {
            $array[$key] = $record->getArrayCopy();
        }
        return $array;
    }
}