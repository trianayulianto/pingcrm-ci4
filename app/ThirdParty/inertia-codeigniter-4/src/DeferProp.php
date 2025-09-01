<?php

namespace Inertia;

class DeferProp implements IgnoreFirstLoad, Mergeable
{
    use MergesProps;

    protected $callback;

    protected $group;

    public function __construct(callable $callback, ?string $group = null)
    {
        $this->callback = $callback;
        $this->group = $group;
    }

    public function group()
    {
        return $this->group;
    }

    public function __invoke()
    {
        return closure_call($this->callback);
    }
}
