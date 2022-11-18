<?php

namespace App\Models;

use App\Entities\Account;
use CodeIgniter\Model;

class AccountModel extends Model
{
    use \Tatter\Relations\Traits\ModelTrait;

    protected $table = 'accounts';
    protected $returnType = Account::class;
    // protected $useSoftDeletes = true;
    protected $allowedFields = [
        'name',
    ];

    // Dates
    protected $useTimestamps = true;
}
