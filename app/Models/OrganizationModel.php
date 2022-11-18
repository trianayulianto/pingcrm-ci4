<?php

namespace App\Models;

use App\Entities\Organization;
use App\Libraries\PaginationHelper;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class OrganizationModel extends Model
{
    use \Tatter\Relations\Traits\ModelTrait;

    protected $table = 'organizations';
    protected $returnType = Organization::class;
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'account_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'region',
        'country',
        'postal_code',
    ];

    // Dates
    protected $useTimestamps = true;

    public function findByParams($search = null, $trashed = null)
    {
        $query = $this->select('*');

        if ($search) {
            $query->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
            ->groupEnd();
        }

        if ($trashed === 'with') {
            $query->withDeleted();
        } elseif ($trashed === 'only') {
            $query->where('deleted_at is', null);
        }

        return [
            'data' => $query->paginate(10),
            'links' => PaginationHelper::getLinks($query->pager)
        ];
    }

    public function findByUserId($userId)
    {
        $query = $this->select(['id', 'name']);

        $query->where('account_id =', static fn (BaseBuilder $builder) => $builder->select('account_id', false)->from('users')->where('id', $userId));

        return $query->findAll();
    }
}
