<?php

namespace Simple\Calendar\ValueObjects;

final readonly class MonthNames implements \ArrayAccess, \IteratorAggregate
{
    private array $names;

    public function __construct(array $names)
    {
        if (count($names) !== 12) {
            throw new \InvalidArgumentException('MonthNames debe contener 12 elementos.');
        }
        $this->names = $names;
    }

    public function offsetExists($offset): bool { return isset($this->names[$offset]); }
    public function offsetGet($offset): mixed { return $this->names[$offset]; }
    public function offsetSet($offset, $value): void { throw new \LogicException('Inmutable'); }
    public function offsetUnset($offset): void { throw new \LogicException('Inmutable'); }
    public function getIterator(): \Traversable { return new \ArrayIterator($this->names); }
    public function toArray(): array { return $this->names; }
}

