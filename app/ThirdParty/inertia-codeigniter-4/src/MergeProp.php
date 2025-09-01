<?php

namespace Inertia;

class MergeProp implements Mergeable
{
    use MergesProps;

    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
        $this->merge = true;
    }

    public function __invoke()
    {
        return is_callable($this->value) ? closure_call($this->value) : $this->value;
    }
}
