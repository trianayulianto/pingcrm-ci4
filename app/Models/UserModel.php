<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Model;

class UserModel extends Model
{
    use \Tatter\Relations\Traits\ModelTrait;

    protected $table = 'users';
    protected $returnType = User::class;
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'account_id',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'owner',
        'photo_path',
        'remember_token',
    ];

    // Dates
    protected $useTimestamps = true;

    public function findByParams($search = null, $role = null, $trashed = null)
    {
        $query = $this->select('*');

        if ($search) {
            $query->groupStart()
                ->like('first_name', $search)
                ->orLike('last_name', $search)
                ->orLike('email', $search)
            ->groupEnd();
        }

        if ($role === 'user') {
            $query->where('owner', '0');
        } elseif ($role === 'owner') {
            $query->where('owner', '1');
        }

        if ($trashed === 'with') {
            $query->withDeleted();
        } elseif ($trashed === 'only') {
            $query->where('deleted_at is', null);
        }

        return $query->findAll();
    }

    public function findAll(?int $limit = 0, int $offset = 0)
    {
        $data = parent::findAll($limit, $offset);

        return $this->addRelations($data);
    }
}
