<?php

namespace App\Models;

use App\Entities\Contact;
use App\Libraries\PaginationHelper;
use CodeIgniter\Model;

class ContactModel extends Model
{
    use \Tatter\Relations\Traits\ModelTrait;

    protected $table = 'contacts';
    protected $returnType = Contact::class;
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'account_id',
        'organization_id',
        'first_name',
        'last_name',
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
                ->like('first_name', $search)
                ->orLike('last_name', $search)
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

    public function findAll(?int $limit = 0, int $offset = 0)
    {
        $data = parent::findAll($limit, $offset);

        return $this->addRelations($data);
    }
}
