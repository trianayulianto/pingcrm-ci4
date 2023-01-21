<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Organization extends Entity
{
    use \Tatter\Relations\Traits\EntityTrait;

    protected $table = 'organizations';
    protected $primaryKey = 'id';

    public function delete()
    {
        $organizationModel = model('OrganizationModel');
        return $organizationModel->delete($this->id);
    }
}
