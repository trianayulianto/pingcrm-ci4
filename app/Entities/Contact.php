<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Contact extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;

    protected $table = 'contacts';
    protected $primaryKey = 'id';

    public function getName()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
