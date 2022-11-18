<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $casts = [
        'email_verified_at' => 'datetime',
        'owner' => 'boolean',
    ];

    public function getName()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function setPassword(string $password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }
}
