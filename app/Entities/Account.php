<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Account extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;

    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [];
}
