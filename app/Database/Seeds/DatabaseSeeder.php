<?php

namespace App\Database\Seeds;

use App\Models\AccountModel;
use App\Models\ContactModel;
use App\Models\OrganizationModel;
use App\Models\UserModel;
use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->db->transStart();

        $accountModel = new AccountModel();
        $accountModel->insert(['name' => 'Acme Corporation']);

        $user = new UserModel();
        $user->insert([
            'account_id' => $accountModel->getInsertID(),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => password_hash('secret', PASSWORD_BCRYPT),
            'owner' => true,
        ]);

        $organizations = [];

        for ($i=0; $i < 100; $i++) {
            $organizations[] = [
                'account_id' => $accountModel->getInsertID(),
                'name' => $this->faker()->company,
                'email' => $this->faker()->companyEmail,
                'phone' => $this->faker()->e164PhoneNumber,
                'address' => $this->faker()->streetAddress,
                'city' => $this->faker()->city,
                'region' => $this->faker()->state,
                'country' => 'US',
                'postal_code' => $this->faker()->postcode,
            ];
        }

        $organizationModel = new OrganizationModel();
        $organizationModel->insertBatch($organizations);

        $organizations = $organizationModel->asArray()->select('id')->findAll();

        $contacts = [];

        for ($i=0; $i < 100; $i++) {
            $contacts[] = [
                'account_id' => $accountModel->getInsertID(),
                'organization_id' => $organizations[array_rand($organizations, 1)]['id'],
                'first_name' => $this->faker()->firstName,
                'last_name' => $this->faker()->lastName,
                'email' => $this->faker()->unique()->safeEmail,
                'phone' => $this->faker()->e164PhoneNumber,
                'address' => $this->faker()->streetAddress,
                'city' => $this->faker()->city,
                'region' => $this->faker()->state,
                'country' => 'US',
                'postal_code' => $this->faker()->postcode,
            ];
        }

        $contactModel = new ContactModel();
        $contactModel->insertBatch($contacts);

        $this->db->transComplete();
    }
}
