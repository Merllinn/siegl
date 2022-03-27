<?php

/**
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Utils;

use BulkGate;

class Iterator implements \Iterator
{
    use BulkGate\Strict;

    /** @var array */
    protected $array = [];

    /** @var int */
    private $position = 0;

    public function __construct(array $rows)
    {
        $this->array = $rows;
        $this->position = 0;
    }

    function get($key)
    {
        return isset($this->array[$key]) ? $this->array[$key] : null;
    }

    function set($key, $value)
    {
        return $this->array[$key] = $value;
    }

    function rewind()
    {
        reset($this->array);
    }

    function current()
    {
        return current($this->array);
    }

    function key()
    {
        return key($this->array);
    }

    function next()
    {
        next($this->array);
    }

    function valid()
    {
        return key($this->array) !== null;
    }

    function count()
    {
        return count($this->array);
    }

    function add($value)
    {
        $this->array[] = $value;
    }
}
