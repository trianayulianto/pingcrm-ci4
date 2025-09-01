<?php

namespace Inertia;

class OptionalProp implements IgnoreFirstLoad
{
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke()
    {
        return closure_call($this->callback);
    }
}
