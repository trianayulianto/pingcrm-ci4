<?php

namespace Inertia;

class AlwaysProp
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __invoke()
    {
        return closure_call($this->value);
    }
}
